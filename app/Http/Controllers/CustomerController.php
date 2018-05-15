<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\CustomerForm;

use App\Helpers\Validator;
use App\Helpers\Error;
use App\Helpers\Unique;

class CustomerController extends Controller
{
    use FormBuilderTrait;

    // list
    public function index()
    {
        $recordes = Customer::where('show', true)->leftJoin('config as c', 'customer.gender', '=', 'config.id')->get();

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
        $record = Customer::find($id);

        if(!$record){
            $error = new Error;
            return $error->notFound();
        }

        return view('users.show');
    }

    // end
}




