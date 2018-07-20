<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Kris\LaravelFormBuilder\FormBuilderTrait;

use App\Helpers\Auth;
use App\Helpers\Error;
use App\Helpers\ConfigList;
use App\Helpers\Logs;

use App\Car;
use App\CarIncome;
use App\Finance;
use App\User;
use App\Forms\CarIncomeForm;

class CarController extends Controller
{
    use FormBuilderTrait;

    private $auth;
    private $ajax_key;

    private function pre() 
    {
        $this->auth = new Auth;

        // 若财务中废弃,则支出中废弃
        $pre =  CarIncome::leftJoin('branches', 'car_incomes.branch', 'branches.id')
                        ->leftJoin('finance', 'car_incomes.finance_id', 'finance.id')
                        ->leftJoin('users', 'finance.user_id', 'users.id')
                        ->leftJoin('cars', 'car_incomes.car_id', 'cars.id')
                        ->leftJoin('config', 'cars.type', 'config.id')
                        ->where(function ($query) {
                                // 分支机构限制
                                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                    $query->Where('car_incomes.branch', $this->auth->branchLimitId());
                                }
                                 
                                // // 起时间点
                                // if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_begin') && Session::get('finance_seek_array')['date_begin'] != '') {
                                //     $query->Where('finance.date', '>=', strtotime(Session::get('finance_seek_array')['date_begin']));
                                // }

                                // // 终时间点
                                // if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_end') && Session::get('finance_seek_array')['date_end'] != '') {
                                //     $query->Where('finance.date', '<=', strtotime(Session::get('finance_seek_array')['date_end']));
                                // }

                                // // 关键词
                                // if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'key') && Session::get('finance_seek_array')['key'] != '') {
                                //     $query->Where('finance.price', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('finance.real_price', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('customers.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('c.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('u.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('i.text', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                // }
                            });
        return $pre;
    }

    // 列表
    public function index()
    {
        // // 授权
        // $auth = new Auth;
        // $auth_error = new Error;
        // if(!$auth->root())  return $auth_error->forbidden();

        $records = $this->pre()->latest('car_incomes.created_at')->paginate(50);
        return view('cars.main', compact('records'));
    }

    // ajax 选择器
    public function selector(Request $request)
    {
        // $key = $request->input('key');
        // $key = 10;
        $this->ajax_key = $request->input('key');

        $json = Car::leftJoin('config', 'cars.type', '=', 'config.id')
                    ->select('car_no', 'config.text as type_text')
                    ->where(function ($query) {
                            $query->Where('car_no', 'LIKE', '%'.$this->ajax_key.'%');
                        })
                    ->get()
                    ->toJson();

        return $json;
    }

    // 加班
    public function income()
    {
        // // 授权
        // $auth = new Auth;
        // $auth_error = new Error;
        // if($auth->admin())  return $auth_error->forbidden();

        // $record = Customer::find($id);
        // $error = new Error;
        // if(!$record) return $error->notFound();

        $form = $this->form(CarIncomeForm::class, [
            'method' => 'POST',
            'url' => '/cars/income/store'
        ]);

        $title = '加班车';
        $icon = 'tag';

        return view('form_with_selector', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 保存
    public function store(Request $request)
    {
        $error = "必选项!";
        if(!$request->car_no) return redirect()->back()->withErrors(['car_no'=>$error])->withInput();
        if(!$request->user_id) return redirect()->back()->withErrors(['user_id'=>$error])->withInput();

        $all = $request->all();
        $user_branch = User::where('work_id', $all['user_id'])->firstOrFail()->branch;
        $car_id = Car::where('car_no', $all['car_no'])->firstOrFail()->id;

        $all['created_by'] = session('id');
        $all['branch'] = $user_branch;

        $finance = array_except($all, ['car_no', 'start', 'hours']);
        $finance['user_id'] = $all['user_id'];
        $finance['real_price'] = $all['price'];
        $finance['date'] = strtotime($all['start']);
        $finance['item'] = 30; // 加班费/租车费id

        $finance_id = Finance::create($finance)->id;

        $car_income = array_except($all, ['user_id', 'car_no', 'price', 'ticket_no']);
        $car_income['car_id'] = $car_id;
        $car_income['start'] = strtotime($all['start']);
        $car_income['finance_id'] = $finance_id;

        CarIncome::create($car_income);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'加班车业务已记录!']);
    }

    // 修理加油
    public function cost()
    {
        # code...
    }



    // end
}








