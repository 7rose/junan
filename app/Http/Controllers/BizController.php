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
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if($auth->admin())  return $auth_error->forbidden();

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

        return view('form_with_selector', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储
    public function store(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if($auth->admin())  return $auth_error->forbidden();
        
        $all = $request->all();
        $all['date'] =  time();
        // $all['date'] =  strtotime($all['date']);
        $all['created_by'] = Session::get('id');

        $biz_check = Biz::where('customer_id', $all['customer_id'])
            ->where('licence_type', $all['licence_type'])
            ->where('finished', false)
            ->get();

        if(count($biz_check)) return view('note')->with('custom', ['color'=>'danger', 'icon'=>'ok', 'content'=>'该学员在本集团内有未完成的相关业务!']);
        
        // $all['checked_by_time'] = time();

        // 财务表
        // 推荐人机构
        // $count_branch = 0;
        // 推荐人
        if($all['user_id'] != '' || $all['user_id'] != null){
            $exists = User::where('work_id', $all['user_id'])->orWhere('mobile', $all['user_id'])->first();
            if(!$exists) {
                $message = "工号或手机号不存在!";
                return redirect()->back()->withErrors(['user_id'=>$message])->withInput();
            }else{
                $all['user_id'] = $exists->id;
                // $count_branch = $exists->branch;
            }
        }

        $finance = array_except($all, ['licence_type', 'class_type']);
        $finance['item'] =  27;
        $finance['checked'] = true;
        $finance['checked_by'] = Session::get('id');
        $finance['checked_by_time'] = time();

        // 若有推荐人, 则财务记录归属推荐人所在机构
        // if($count_branch != 0) $finance['branch'] = $count_branch;

         // 存储业务表
        $biz = array_except($all, ['price', 'real_price', 'user_id', 'ticket_no']);

        $biz_id = Biz::create($biz)->id;
        $finance['biz_id'] =  $biz_id;

        Finance::create($finance);

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

        $all =$request->all();

        $record = Biz::find($id);

        if($record->class_id && $all['licence_type'] != $record->licence_type) {
            return view('note')->with('custom', ['color'=>'danger', 'icon'=>'info', 'content'=>'该业务已开班, 不能修改证照类型!']);
        }
        $all['user_id'] = null;
        
        $record->update($all);

        // $record->update(['class_type'=>$request->class_type, 'branch'=>$request->branch, 'user_id'=>null]);
        // if($request->branch != $record->branch) $record->update(['user_id'=>null]);

        return redirect('/customer/'.$record->customer_id);
    }

    // 设置教练
    public function teacher($key)
    {
        $key_array = explode(',', $key);
        $record = Biz::find($key_array[0]);
        $record->update(['user_id'=>$key_array[1]]);

        // $path = $key_array[2];
        $path = str_replace(";","/",$key_array[2]);

        return redirect('/'.$path);

        // return redirect('/customer/'.$record->customer_id);
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

    // 设置准考证号
    public function setFileId(Request $request)
    {
        $id = $request->input('id');
        $file_id = $request->input('file_id');

        $record = Biz::find($id);
        $record->update(['file_id'=>$file_id]);

        return $file_id;
    }

    // 清除准考证号
    public function cancelFileId($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $record = Biz::find($id);
        $record->update(['file_id'=>null]);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'该学员准考证记录已经撤销!']);
    }

    // 重打成绩单
    public function reprint($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $record = Biz::find($id);
        $record->update(['printed'=>false]);
        return redirect('/customer/'.$record->customer_id);
    }

    // end
}






