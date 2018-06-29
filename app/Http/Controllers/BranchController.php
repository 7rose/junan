<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Branch;
use App\Helpers\Auth;
use App\Helpers\Error;

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
        return 200;
    }

    // end
}
