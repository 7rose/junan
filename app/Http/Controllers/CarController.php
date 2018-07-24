<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Excel;
use Kris\LaravelFormBuilder\FormBuilderTrait;

use App\Helpers\Auth;
use App\Helpers\Error;
use App\Helpers\ConfigList;
use App\Helpers\Logs;

use App\Car;
use App\CarIncome;
use App\Forms\CarIncomeForm;
use App\CarCost;
use App\Forms\CarCostForm;
use App\Finance;
use App\User;

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
                        ->leftJoin('users as c', 'car_incomes.created_by', 'c.id')
                        ->leftJoin('cars', 'car_incomes.car_id', 'cars.id')
                        ->leftJoin('config', 'cars.type', 'config.id')
                        ->select(
                            'car_incomes.*',
                            'branches.text as branch_text',
                            'finance.real_price',
                            'finance.abandon',
                            'finance.ticket_no',
                            'users.name as user_name',
                            'c.name as created_by_name',
                            'cars.car_no',
                            'config.text as type_text'
                        )
                        ->where(function ($query) {
                                if(Session::has('cars_date_start')){
                                    $query->where('car_incomes.start', '>=', strtotime(Session::get('cars_date_start')));
                                }

                                if(Session::has('cars_date_end')){
                                    $query->where('car_incomes.start', '<', strtotime(Session::get('cars_date_end')));
                                }

                                if(Session::has('cars_key')){
                                    $query->where('cars.car_no', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('users.name', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('config.text', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('branches.text', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('car_incomes.content', 'LIKE', '%'.Session::get('cars_key').'%');
                                }

                                // 分支机构限制
                                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                    $query->Where('car_incomes.branch', $this->auth->branchLimitId());
                                }
                            });
        return $pre;
    }

    // 加油和维修列表
    private function pre2() 
    {
        $this->auth = new Auth;

        // 若财务中废弃,则支出中废弃
        $pre =  CarCost::leftJoin('branches', 'car_costs.branch', 'branches.id')
                        ->leftJoin('finance', 'car_costs.finance_id', 'finance.id')
                        ->leftJoin('users', 'finance.user_id', 'users.id')
                        ->leftJoin('users as c', 'car_costs.created_by', 'c.id')
                        ->leftJoin('cars', 'car_costs.car_id', 'cars.id')
                        ->leftJoin('config', 'cars.type', 'config.id')
                        ->leftJoin('config as i', 'finance.item', 'i.id')
                        ->select(
                            'car_costs.*',
                            'branches.text as branch_text',
                            'finance.date',
                            'finance.real_price',
                            'finance.abandon',
                            'finance.ticket_no',
                            'users.name as user_name',
                            'c.name as created_by_name',
                            'cars.car_no',
                            'config.text as type_text',
                            'i.text as item_text'
                        )
                        ->where(function ($query) {
                                if(Session::has('cars_date_start')){
                                    $query->where('finance.date', '>=', strtotime(Session::get('cars_date_start')));
                                }

                                if(Session::has('cars_date_end')){
                                    $query->where('finance.date', '<', strtotime(Session::get('cars_date_end')));
                                }

                                if(Session::has('cars_key')){
                                    $query->where('cars.car_no', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('users.name', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('config.text', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('branches.text', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('i.text', 'LIKE', '%'.Session::get('cars_key').'%');
                                    $query->orWhere('car_costs.content', 'LIKE', '%'.Session::get('cars_key').'%');
                                }

                                // 分支机构限制
                                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                    $query->Where('car_costs.branch', $this->auth->branchLimitId());
                                }
                            });
        return $pre;
    }

    // 查询
    public function seek(Request $request)
    {
        $all = $request->all();

        if($request->date_start != '') {
            Session::put('cars_date_start', $request->date_start);
        }else{
            if(Session::has('cars_date_start')) Session::forget('cars_date_start');
        }

        if($request->date_end != '') {
            Session::put('cars_date_end', $request->date_end);
        }else{
            if(Session::has('cars_date_end')) Session::forget('cars_date_end');
        }

        if($request->key != '') {
            Session::put('cars_key', $request->key);
        }else{
            if(Session::has('cars_key')) Session::forget('cars_key');
        }

        // return $this->index();
        return redirect('/'.$request->to_path);
    }

    // 重置查询条件
    public function resetSeek($url)
    {
        if(Session::has('cars_date_start')) Session::forget('cars_date_start');
        if(Session::has('cars_date_end')) Session::forget('cars_date_end');
        if(Session::has('cars_key')) Session::forget('cars_key');

        return redirect('/cars/'.$url);
    }

    // 加班车列表
    public function incomeIndex()
    {
        $records = $this->pre()
                        ->latest('car_incomes.created_at')
                        ->paginate(50);

        // print_r($records);
        // $all = CarIncome::count();
        $all = $this->pre()->get()->count();

        return view('cars.main', compact('records', 'all'));
    }

    // 加油修理列表
    public function costIndex()
    {
        $records = $this->pre2()->latest('car_costs.created_at')->paginate(50);
        // $all = CarCost::count();
        $all = $this->pre2()->get()->count();
        return view('cars.main_costs', compact('records', 'all'));
    }

    // 列表
    public function index()
    {
        // // 授权
        // $auth = new Auth;
        // $auth_error = new Error;
        // if(!$auth->root())  return $auth_error->forbidden();

        $records = $this->pre()->latest('car_incomes.created_at')->paginate(50);

        print_r($records);
        // return view('cars.main', compact('records'));
        // return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'车辆模块即将上线!']);
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
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->branchLimit())  return $auth_error->forbidden();

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
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->branchLimit())  return $auth_error->forbidden();

        $error = "必选项!";
        if(!$request->car_no) return redirect()->back()->withErrors(['car_no'=>$error])->withInput();
        if(!$request->user_id) return redirect()->back()->withErrors(['user_id'=>$error])->withInput();

        $all = $request->all();
        $user = User::where('work_id', $all['user_id'])->firstOrFail();
        $car_id = Car::where('car_no', $all['car_no'])->firstOrFail()->id;

        $all['created_by'] = session('id');
        $all['branch'] = User::find(session('id'))->branch;
        // $all['branch'] = $user->branch;

        $finance = array_except($all, ['car_no', 'start', 'hours']);
        $finance['user_id'] = $user->id;
        $finance['real_price'] = $all['price'];
        $finance['checked'] = true;
        $finance['checked_by'] = $all['created_by'];
        $finance['checked_by_time'] = time();
        $finance['date'] = strtotime($all['start']);
        $finance['item'] = 30; // 加班费/租车费id

        $finance_id = Finance::create($finance)->id;

        $car_income = array_except($all, ['user_id', 'car_no', 'price', 'ticket_no']);
        $car_income['car_id'] = $car_id;
        $car_income['start'] = strtotime($all['start']);
        $car_income['finance_id'] = $finance_id;

        $new = CarIncome::create($car_income);

        // 日志
        $log_content = "车辆: 新登记加班车. 序号:".$new->id;
        $log_level = "info";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return redirect('/cars/incomes');

        // return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'加班车业务已记录!']);

    }

    // 修理加油
    public function cost()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->branchLimit())  return $auth_error->forbidden();

        $form = $this->form(CarCostForm::class, [
            'method' => 'POST',
            'url' => '/cars/cost/store'
        ]);

        $title = '车辆维修和加油';
        $icon = 'wrench';

        return view('form_with_selector', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 保存修理加油
    public function costStore(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->branchLimit())  return $auth_error->forbidden();

        $error = "必选项!";
        if(!$request->car_no) return redirect()->back()->withErrors(['car_no'=>$error])->withInput();
        if(!$request->user_id) return redirect()->back()->withErrors(['user_id'=>$error])->withInput();

        $all = $request->all();
        $user = User::where('work_id', $all['user_id'])->firstOrFail();
        $car_id = Car::where('car_no', $all['car_no'])->firstOrFail()->id;

        $all['created_by'] = session('id');
        $all['branch'] = User::find(session('id'))->branch;
        // $all['branch'] = $user->branch;

        $finance = array_except($all, ['car_no']);
        $finance['in'] = false;
        $finance['user_id'] = $user->id;
        $finance['real_price'] = $all['price'];
        $finance['checked'] = true;
        $finance['checked_by'] = $all['created_by'];
        $finance['checked_by_time'] = time();
        $finance['date'] = strtotime($all['date']);

        $finance_id = Finance::create($finance)->id;

        $car_cost = array_except($all, ['user_id', 'car_no', 'price', 'ticket_no', 'item', 'date']);
        $car_cost['car_id'] = $car_id;
        $car_cost['finance_id'] = $finance_id;

        $new = CarCost::create($car_cost);

        // 日志
        $log_content = "车辆: 新登记加油或修理费. 序号:".$new->id;
        $log_level = "info";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return redirect('/cars/costs');

        // return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'车辆支出已记录!']);
    }

    // 加班车表格
    public function incomesExcel()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        
        $cellData = [
            ['牌号', '类型', '驾校', '教练员', '开始时间', '时长', '价格', '票号', '操作人', '时间', '备注'],
        ];

        $records = $this->pre()
                        ->where('finance.abandon', false)
                        ->latest('car_incomes.created_at')
                        ->get();

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        $record->car_no ,
                                        $record->type_text ,
                                        $record->branch_text ,
                                        $record->user_name ,
                                        date('Y-m-d H:i:s', $record->start) ,
                                        $record->hours ,
                                        $record->real_price ,
                                        $record->ticket_no ,
                                        $record->created_by_name ,
                                        $record->created_at ,
                                        $record->content 
                                    ]);
            }
        }
        $file_name = '车辆-加班'.date('Y-m-d', time());

        // 日志
        $log_content = "车辆: 下载加班车Excel文件(可能为查询结果)";
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        Excel::create($file_name,function($excel) use ($cellData){
            $excel->sheet('列表', function($sheet) use ($cellData){
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

    // 加油维修表格
    public function costsExcel()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $cellData = [
            ['牌号', '类型', '驾校', '教练员', '支出类型', '价格', '时间', '票号', '操作人', '时间', '备注'],
        ];

        $records = $this->pre2()
                        ->where('finance.abandon', false)
                        ->latest('car_costs.created_at')
                        ->get();

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        $record->car_no ,
                                        $record->type_text ,
                                        $record->branch_text ,
                                        $record->user_name ,
                                        $record->item_text ,
                                        $record->real_price ,
                                        date('Y-m-d', $record->date) ,
                                        $record->ticket_no ,
                                        $record->created_by_name ,
                                        $record->created_at ,
                                        $record->content
                                    ]);
            }
        }
        $file_name = '车辆-维修和加油'.date('Y-m-d', time());

        // 日志
        $log_content = "车辆: 下载加油和维修Excel文件(可能为查询结果)";
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        Excel::create($file_name,function($excel) use ($cellData){
            $excel->sheet('列表', function($sheet) use ($cellData){
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

    // end
}








