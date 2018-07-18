<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Helpers\Auth;
use App\Helpers\Error;
use App\Helpers\Logs;

class BranchController extends Controller
{
    public function index()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

       $records =  Branch::orderBy('created_at', 'desc')->get();

       return view('branch.index')
                    ->with('records', $records);
    }

    // 设置隐藏/显示
    public function set($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

        if($id==1) return $auth_error->forbidden();

        $target = Branch::find($id);

        $target->update(['show'=> $target->show ? false : true]);
        // 日志
        $log_content = "系统: 打开/关闭分支机构";
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return redirect('/branch');
    }

    // 新机构
    public function add(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

        $text = $request->input('text');
        $parent_id = 1;

        Branch::create(['parent_id'=>$parent_id, 'text'=>$text]);

        // 日志
        $log_content = "系统: 添加分支机构 - ".$request->input('text');
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return 200;
    }

    // end
}
