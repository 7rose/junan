<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\BizForm;
use App\Forms\BizEditForm;
use App\Helpers\Error;
use App\Helpers\Auth;
// use App\Helpers\Unique;
use App\Customer;
use App\Finance;
use App\Biz;
use App\User;

class BizController extends Controller
{
     use FormBuilderTrait;
    // 新业务
    public function create($id=0)
    {
        $error = new Error;

        if($id == 0) return redirect('/customer');
        $record = Customer::find($id);
        if(!$record) return $error->notFound();

        $form = $this->form(BizForm::class, [
            'method' => 'POST',
            'url' => route('biz.store')
        ]);

        $title = '新业务 - '.$record->name;
        $icon = 'credit-card';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储
    public function store(Request $request)
    {
        $all = $request->all();
        $all['date'] =  strtotime($all['date']);
        $all['created_by'] = Session::get('id');

        // 财务表
        // 推荐人
        if($all['user_id'] != '' || $all['user_id'] != null){
            $exists = User::where('work_id', $all['user_id'])->orWhere('mobile', $all['user_id'])->first();
            if(!$exists) {
                $message = "工号或手机号不存在!";
                return redirect()->back()->withErrors(['user_id'=>$message])->withInput();
            }else{
                $all['user_id'] = $exists->id;
            }
        }

        $finance = array_except($all, ['licence_type', 'class_type']);
        $finance['item'] =  27;
        Finance::create($finance);
         // 存储业务表
        $biz = array_except($all, ['price', 'real_price', 'user_id']);
        Biz::create($biz);

        return redirect('/customer/'.$all['customer_id']);
    }


    // 认领
    public function claim (Request $request)
    {
        $auth = new Auth;
        $error = new Error;
        if(!$auth->branchLimit()) return "无权操作";

        $id = $request->input('id');

        Biz::where('customer_id', $id)->first()->update(['branch'=> $auth->branchLimit()]);
        
        return "认领成功!";
    }

    // 修改
    public function edit($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $record = Biz::find($id);

        $form = $this->form(BizEditForm::class, [
            'method' => 'POST',
            'model' => $record,
            //'url' => route('fashion.update')
            'url' => '/biz/update/'.$id
        ]);

        return view('form', compact('form'))->with('custom',['title'=>'信息修改 - '.$record->name, 'icon'=>'cog']);
    }

    // 更新
    public function update(Request $request, $id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $record = Biz::find($id);
        $record->update(['class_type'=>$request->class_type, 'branch'=>$request->branch, 'user_id'=>null]);

        // if($request->branch != $record->branch) $record->update(['user_id'=>null]);

        return redirect('/customer/'.$record->customer_id);
    }

    // 设置教练
    public function teacher($key)
    {
        $key_array = explode('-', $key);
        $record = Biz::find($key_array[0]);
        $record->update(['user_id'=>$key_array[1]]);
        return redirect('/customer/'.$record->customer_id);
    }

    // 关闭业务
    public function close($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $record = Biz::find($id);
        $record->update(['finished'=>true]);
        return redirect('/customer/'.$record->customer_id);
    }

    // 打开业务
    public function open($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

        $record = Biz::find($id);
        $record->update(['finished'=>false]);
        return redirect('/customer/'.$record->customer_id);
    }

    // end
}






