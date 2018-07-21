<?php

namespace App\Http\Controllers;

use Session;
use DB;
use Hash;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Finance;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\UserForm;
use App\Forms\UserLoginForm;
use App\Forms\UserSeekForm;
use App\Forms\UserPasswordForm;

use App\Helpers\Validator;
use App\Helpers\Error;
use App\Helpers\Unique;
use App\Helpers\Auth;
use App\Helpers\Logs;

class UserController extends Controller
{
    use FormBuilderTrait;

    private $auth;
    private $ajax_key;

    private function prepare() 
    {
        $this->auth = new Auth;

        $records = User::leftJoin('config as g', 'users.gender', '=', 'g.id')
                    ->leftJoin('config as t', 'users.user_type', '=', 't.id')
                    ->leftJoin('branches', 'users.branch', '=', 'branches.id')
                    ->leftJoin('users as c', 'users.created_by', '=', 'c.id')
                    // ->leftJoin('biz', 'users.id', '=', 'biz.user_id')
                    // ->leftJoin('finance', 'users.id', '=', 'finance.user_id')
                    ->select(
                        'users.*', 
                        'g.text as gender_text', 
                        't.text as user_type_text', 
                        'branches.text as branch_text', 
                        'c.name as created_by_text')
                    ->where(function ($query) {
                            // 分支机构限制
                            if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                $query->Where('users.branch', $this->auth->branchLimitId());
                            }
                            
                            // seek
                            if(Session::has('user_seek_array') && array_has(Session::get('user_seek_array'), 'key') && Session::get('user_seek_array')['key'] != '') {
                                $query->Where('users.work_id', 'LIKE', '%'.Session::get('user_seek_array')['key'].'%');
                                $query->orWhere('users.name', 'LIKE', '%'.Session::get('user_seek_array')['key'].'%');
                                $query->orWhere('users.mobile', 'LIKE', '%'.Session::get('user_seek_array')['key'].'%');
                                $query->orWhere('branches.text', 'LIKE', '%'.Session::get('user_seek_array')['key'].'%');
                                $query->orWhere('t.text', 'LIKE', '%'.Session::get('user_seek_array')['key'].'%');
                                $query->orWhere('users.content', 'LIKE', '%'.Session::get('user_seek_array')['key'].'%');
                            }
                        });
        return $records;
    }

    // index  
    public function index()
    {
        $form = $this->form(UserSeekForm::class, [
            'method' => 'POST',
            'url' => route('user.seek')
        ]);

        $records = $this->prepare()
                    ->orderBy('users.branch')
                    ->orderBy('users.user_type')
                    ->orderBy('users.work_id')
                    ->orderBy('users.auth_type')
                    // ->orderBy('users.created_at', 'desc')
                    ->paginate(50);

        $all = $this->prepare()
                        ->get()
                        ->count();

        return view('users.index', compact('form'))
                    ->with('all', $all)
                    ->with('records', $records);
    }

    // 查询条件
    public function seek(Request $request)
    {
        $user_seek_array = [];
        if($request->has('key')) $user_seek_array = array_add($user_seek_array, 'key', $request->key);
        if($request->has('branch')) $user_seek_array = array_add($user_seek_array, 'branch', $request->branch);
        Session::put('user_seek_array', $user_seek_array);
        return redirect('/user');
    }

    // 查询重置
    public function seekReset()
    {
        if(Session::has('user_seek_array')) Session::forget('user_seek_array');
        return redirect('/user');
    }

    // 登录
    public function login()
    {
        $form = $this->form(UserLoginForm::class, [
            'method' => 'POST',
            'url' => route('user.check')
        ]);

        $title = '登录';
        $icon = 'barcode';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 退出
    public function logout()
    {

        // // 日志
        // $log_array = ['content'=>"退出"];
        // $log_put = new Logs;
        // $log_put->put($log_array);
        
        Session::flush();
        // if (Cookie::has('id')) Cookie::queue('id', '', -1);
        return redirect('/');
    }

    // 登录检查
    public function check(Request $request)
    {
        $form = $this->form(UserLoginForm::class);

        // 用户名
        $record = User::where('work_id', $request->id)->orWhere('mobile', $request->id)->first();
        if(!$record) return redirect()->back()->withErrors(['id'=>'此ID不存在!'])->withInput();

        // 密码
        if (!Hash::check($request->password, $record->password)) return redirect()->back()->withErrors(['password'=>'密码错误!'])->withInput();
        Session::put('id', $record->id);
        // return redirect($request->path);

        // 日志
        $log_array = ['content'=>"登录"];
        $log_put = new Logs;
        $log_put->put($log_array);
        
        if(Session::has('target_url')) {
            $target_url = Session::get('target_url');
            Session::forget('target_url');
            return redirect($target_url);
        }else{
            return redirect('/doc');
        }
    }

    // 锁定
    public function lock($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin() || !$auth->master($id))  return $auth_error->forbidden();

        $target = User::find($id);
        $target->update(['locked' => true]);

        // 日志
        $log_content = "成员: 锁定: ".$target->name.'/'.$target->work_id;
        $log_level = "warning";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return redirect('/user/'.$id);
    }

    // 解锁
    public function unlock($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin() || !$auth->master($id))  return $auth_error->forbidden();
        
        $target = User::find($id);
        $target->update(['locked' => false]);

        // 日志
        $log_content = "成员: 解锁: ".$target->name.'/'.$target->work_id;
        $log_level = "warning";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return redirect('/user/'.$id);
    }


    // 锁定提示
    public function lockInfo()
    {
        $error = new Error;
        return $error->locked();
    }

    // 欢迎
    public function welcome()
    {
        return view('users.welcome');
    }

    // 重设密码
    public function resetPassword()
    {
        $form = $this->form(UserPasswordForm::class, [
            'method' => 'POST',
            'url'    => route('password.store')
        ]);

        $title = '重设密码';
        $icon = 'cog';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 处理重设密码
    public function updatePassword(Request $request)
    {
        $form = $this->form(UserPasswordForm::class);
        if($request->password != $request->password_confirmed) return redirect()->back()->withErrors(['password_confirmed'=>'2次输入不一致!'])->withInput();
        User::find(Session::get('id'))->update(['password'=>bcrypt($request->password), 'new'=>false]);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'您的密码修改成功!']);
    }

    // 管理员设置用户密码
    public function passwordHelp($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin() || !$auth->master($id))  return $auth_error->forbidden();

        $record = User::find($id);
        if(!$record) return $auth_error->notFound();

        $password = $record->work_id.date("md");
        $record->update(['password'=>bcrypt($password), 'new'=>true]);

        // 日志
        $log_content = "成员: 重置用户密码: ".$record->name.'/'.$record->work_id;
        $log_level = "warning";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>$record->name.'的密码修改成功!  格式为['.$record->name.'工号+本月+本日]']);
    }


    // 新员工
    public function create()
    {
        $form = $this->form(UserForm::class, [
            'method' => 'POST',
            'url' => route('user.store')
        ]);

        $title = '新员工';
        $icon = 'user';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储
    public function store(Request $request)
    {
        $all = $request->all();
        $form = $this->form(UserForm::class);

        $validate = new Validator;
        $exists = new Unique;
        // 身份证格式检查
        // if(!$validate->checkIdNumber($all['id_number'])) return redirect()->back()->withErrors(['id_number'=>'身份证错误!'])->withInput();
        // 身份证唯一性检查
        // $resault = $exists->exists($all['id_number'], 'id_number', 'users');
        // if($resault) {
        //     $message = "此身份证号已存在! <a href=\"/customer/".$resault."\">若确认输入无误, 可转到已有记录</a>";
        //     return redirect()->back()->withErrors(['id_number'=>$message])->withInput();
        // }
        // 手机号格式检查
        if(!$validate->checkMobile($all['mobile'])) return redirect()->back()->withErrors(['mobile'=>'手机号错误!'])->withInput();
        // 手机号重复性检查
        $resault = $exists->exists($all['mobile'], 'mobile', 'users');
        if($resault) {
            $message = "此手机号已存在! <a href=\"/user/".$resault."\">若确认输入无误, 可转到已有记录</a>";
            return redirect()->back()->withErrors(['mobile'=>$message])->withInput();
        }

        $all['work_id'] = $validate->getWorkId();
        $all['created_by'] = Session::get('id');
        $all['password'] = bcrypt($all['work_id'].$all['work_id']);

        $id = User::create($all);

        // 日志
        $log_content = "成员: 新建: ".$all['work_id'].'/'.$all['name'];
        $log_level = "warning";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return redirect('/user/'.$id->id);
    }

    // 修改信息
    public function edit($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;

        $record = User::find($id);

        $form = $this->form(UserForm::class, [
            'method' => 'POST',
            'model' => $record,
            //'url' => route('fashion.update')
            'url' => '/user/update/'.$id
        ]);


        if(($auth->admin() && $auth->master($id)) || $auth->self($id)){
            return view('form', compact('form'))->with('custom',['title'=>'信息修改 - '.$record->name, 'icon'=>'cog']);
        }else{
           return $auth_error->forbidden(); 
        }         
    }

    // 执行修改
    public function update(Request $request, $id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(($auth->admin() && $auth->master($id)) || $auth->self($id)){
        
            $all = $request->all();
            $form = $this->form(UserForm::class);

            $validate = new Validator;
            // // 身份证格式检查
            // if(!$validate->checkIdNumber($all['id_number'])) return redirect()->back()->withErrors(['id_number'=>'身份证错误!'])->withInput();
            // 身份证唯一性检查
            $exists = new Unique;
            if(!$validate->checkMobile($all['mobile'])) return redirect()->back()->withErrors(['mobile'=>'手机号错误!'])->withInput();
            // 手机号重复性检查
            $target = User::find($id);

            $resault = $exists->exists($all['mobile'], 'mobile', 'users');
            if($resault && $target->mobile != $all['mobile']) {
                $message = "此手机号已存在! <a href=\"/user/".$resault."\">若确认输入无误, 可转到已有记录</a>";
                return redirect()->back()->withErrors(['mobile'=>$message])->withInput();
            }
            // 手机号格式检查
            if(!$validate->checkMobile($all['mobile'])) return redirect()->back()->withErrors(['mobile'=>'手机号错误!'])->withInput();

            $target->update($all);

            // 日志
            $log_content = "成员: 修改: ".$target->work_id.'/'.$target->name;
            $log_level = "warning";
            $log_put = new Logs;
            $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

            return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'用户信息修改成功!']);
        }else{
            return $auth_error->forbidden(); 
        }
    }

    // 个人
    public function show($id=0)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin() && ($id != 0 && !$auth->sameBranch($id)))  return $auth_error->forbidden();

        if($id===0) return redirect('/user');
        $record = User::leftJoin('config as g', 'users.gender', '=', 'g.id')
                    ->leftJoin('config as ut', 'users.user_type', '=', 'ut.id')
                    ->leftJoin('config as at', 'users.auth_type', '=', 'at.id')
                    ->leftJoin('branches', 'users.branch', '=', 'branches.id')
                    ->leftJoin('users as c', 'users.created_by', '=', 'c.id')
                    ->select('users.*', 'g.text as gender_text', 'ut.text as user_type_text', 'at.text as auth_type_text', 'branches.text as branch_text', 'c.name as created_by_text')
                    ->find($id);

        if(!$record){
            $error = new Error;
            return $error->notFound();
        }

        // 本月
        $now = Carbon::now();
        $start = $now->startOfMonth(); 
        $end = $now->copy()->endOfMonth(); 

        $finance = DB::table('finance')
                            ->where('finance.user_id', $id)
                            ->whereBetween('date', [strtotime($start), strtotime($end)])
                            ->leftJoin('config', 'finance.item', '=', 'config.id')
                            ->leftJoin('customers', 'finance.customer_id', '=', 'customers.id')
                            ->leftJoin('users as c', 'finance.created_by', '=', 'c.id')
                            ->leftJoin('users as a', 'finance.user_id', '=', 'a.id')
                            ->leftJoin('branches', 'finance.branch', '=', 'branches.id')
                            ->select(
                                    'finance.*', 
                                    'customers.name as customer_id_text', 
                                    'config.text as item_text', 
                                    'c.name as created_by_text', 
                                    'a.name as user_id_text', 
                                    'branches.text as branch_text'
                                )
                            ->get();

        $biz = DB::table('biz')
                        ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                        ->leftJoin('branches', 'biz.branch', '=', 'branches.id')
                        ->leftJoin('classes', 'biz.class_id', '=', 'class_id')
                        ->leftJoin('branches as cb', 'classes.branch', '=', 'cb.id')
                        ->leftJoin('config', 'biz.licence_type', '=', 'config.id')
                        ->leftJoin('users', 'biz.user_id', '=', 'users.id')
                        ->where('biz.finished', false)
                        ->where('biz.user_id', $id)
                        ->select(
                            'customers.name as customer_name', 
                            'customers.mobile as customer_mobile', 
                            'customers.id_number as customer_id_number', 
                            'config.text as licence_type_text', 
                            'branches.text as branch_text', 
                            'users.name as user_name', 
                            'cb.text as class_branch_text', 
                            'classes.class_no as class_no'
                            )
                        ->get();


        return view('users.show')
                        ->with('record', $record)
                        ->with('biz', $biz)
                        ->with('finance', $finance);
    }

    // 输出Execl
    public function seekToExcel()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();
        
        $cellData = [
            ['工号', '驾校', '姓名', '手机', '备注'],
        ];

        $records = $this->prepare()
                        ->orderBy('users.branch')
                        ->orderBy('users.user_type')
                        ->orderBy('users.work_id')
                        ->get();

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        $record->work_id,
                                        $record->branch_text, 
                                        $record->name, 
                                        $record->mobile, 
                                        $record->content
                                    ]);
            }
        }
        $file_name = '成员'.date('Y-m-d', time());

        // 日志
        $log_content = "成员: 下载成员Excel文件(可能为查询结果)";
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

    // 设置分支机构
    public function setBranch(Request $request, $id)
    {
        // 授权
        $auth = new Auth;
        $error = new Error;

        if($auth->branchLimit()) {
            return $error->forbidden();
        }else {
            Session::put('branch_set', $id);
        }
        $text = DB::table('branches')->find($id)->text;
        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'已经设置业务范围为: '.$text]);
    }


    // docs
    public function doc()
    {
        return view('users.doc');
    }

    // ajax 选择器
    public function selector(Request $request)
    {
        // $key = $request->input('key');
        // $key = 10;
        $this->ajax_key = $request->input('key');

        $json = User::select('name', 'work_id', 'mobile')
                    ->where(function ($query) {
                            // 分支机构限制
                            // if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                            //     $query->Where('branch', $this->auth->branchLimitId());
                            // }
                            $query->Where('work_id', 'LIKE', '%'.$this->ajax_key.'%');
                            $query->orWhere('name', 'LIKE', '%'.$this->ajax_key.'%');
                            $query->orWhere('mobile', 'LIKE', '%'.$this->ajax_key.'%');
                        })
                    ->get()
                    ->toJson();

        return $json;
    }

    // end
}





