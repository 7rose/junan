<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Customer;
use App\Biz;
use App\Finance;
use DB;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\CustomerForm;
use App\Forms\CustomerSeekForm;

use App\Helpers\Validator;
use App\Helpers\Error;
use App\Helpers\Unique;

class CustomerController extends Controller
{
    use FormBuilderTrait;

    // index 
    public function index()
    {
        $form = $this->form(CustomerSeekForm::class, [
            'method' => 'POST',
            'url' => route('customer.seek')
        ]);

        $records = Customer::where(function ($query) { 
                            // // admin
                            // if(!$this->tap->realRoot()) $query->where('staff.id', '>', 1);
                            // if(!$this->tap->isAdmin()) {
                            //     $query->where('staff.hide', false);
                            //     $query->whereIn('staff.department', $this->tap->allVisibleDepartments());
                            // }

                            // 关键词
                            if(Session::has('seek_array') && array_has(Session::get('seek_array'), 'key') && Session::get('seek_array')['key'] != '') {
                                $query->Where('customers.name', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                                $query->orWhere('customers.mobile', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                                $query->orWhere('customers.id_number', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                                $query->orWhere('customers.address', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                                $query->orWhere('customers.location', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                                $query->orWhere('customers.content', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                            }
                        })
                    // ->Where('customers.name', 'LIKE', '%'.Input::get('key').'%')
                    ->leftJoin('config', 'customers.gender', '=', 'config.id')
                    ->select('customers.*', 'config.text')
                    // ->get();
                    ->paginate(30);

        return view('customers.index', compact('form'))->with('records', $records);
        // return view('customers.index')->with('records', $records);
    }

    // 查询条件
    public function seek(Request $request)
    {
        $seek_array = [];
        if($request->has('key')) $seek_array = array_add($seek_array, 'key', $request->key);
        Session::put('seek_array', $seek_array);
        return redirect('/customer');
    }

    // 查询重置
    public function seekReset()
    {
        if(Session::has('seek_array')) Session::forget('seek_array');
        return redirect('/customer');
    }


    // 新学员表单
    public function create()
    {
        $form = $this->form(CustomerForm::class, [
            'method' => 'POST',
            'url' => route('customer.store')
        ]);

        $title = '新学员';
        $icon = 'user';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储
    public function store(Request $request)
    {
        $all = $request->all();
        $form = $this->form(CustomerForm::class);

        $validate = new Validator;
        // 身份证格式检查
        if(!$validate->checkIdNumber($all['id_number'])) return redirect()->back()->withErrors(['id_number'=>'身份证错误!'])->withInput();
        // 身份证唯一性检查
        $exists = new Unique;
        $resault = $exists->exists($all['id_number'], 'id_number', 'customers');
        if($resault) {
            $message = "此身份证号已存在! <a href=\"/customer/".$resault."\">若确认输入无误, 可转到已有记录</a>";
            return redirect()->back()->withErrors(['id_number'=>$message])->withInput();
        }
        // 手机号格式检查
        if(!$validate->checkMobile($all['mobile'])) return redirect()->back()->withErrors(['mobile'=>'手机号错误!'])->withInput();

        $id = Customer::create($all);
        return redirect('/customer/'.$id->id);
    }

    // 个人
    public function show($id=0)
    {
        if($id===0) return redirect('/customer');
        $record = Customer::leftJoin('config', 'customers.gender', '=', 'config.id')
                          ->select('customers.*', 'config.text as gender_text')
                          ->find($id);

        if(!$record){
            $error = new Error;
            return $error->notFound();
        }

        $biz = Biz::where('biz.customer_id', $id)
                   ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                   ->leftJoin('config as lt', 'biz.licence_type', '=', 'lt.id')
                   ->leftJoin('config as ct', 'biz.class_type', '=', 'ct.id')
                   ->leftJoin('users', 'biz.created_by', '=', 'users.id')
                   ->select('biz.*', 'customers.name as customer_name', 'lt.text as licence_type_text', 'ct.text as class_type_text', 'users.name as created_by_text')
                   ->get();

        $finance = DB::table('finance')
                            ->where('finance.customer_id', $id)
                            ->leftJoin('config', 'finance.item', '=', 'config.id')
                            ->leftJoin('customers', 'finance.customer_id', '=', 'customers.id')
                            ->leftJoin('users as c', 'finance.created_by', '=', 'c.id')
                            ->leftJoin('users as a', 'finance.user_id', '=', 'a.id')
                            ->select('finance.*', 'config.text as item_text', 'customers.name as customer_name', 'c.name as created_by_text', 'a.name as user_id_text')
                            ->get();

        $to_out = Finance::where('customer_id', $id)->where('in', false)->sum('price');
        $out = Finance::where('customer_id', $id)->where('in', false)->sum('real_price');

        $to_in =Finance::where('customer_id', $id)->where('in', true)->sum('price');
        $in =Finance::where('customer_id', $id)->where('in', true)->sum('real_price');

        $rest = $out - $to_out + $to_in - $in;

        $finance_info = ['to_out'=> $to_out, 'out'=> $out, 'to_in'=>$to_in, 'in'=>$in, 'rest'=>$rest];


        return view('customers.show')
                    ->with('record', $record)
                    ->with('finance', $finance)
                    ->with('finance_info', $finance_info)
                    ->with('biz', $biz);
    }

    // end
}




