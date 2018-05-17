<?php

namespace App\Http\Controllers;

use Session;
use Hash;
use Illuminate\Http\Request;
use App\User;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\UserForm;
use App\Forms\UserLoginForm;
use App\Forms\UserSeekForm;

use App\Helpers\Validator;
use App\Helpers\Error;
use App\Helpers\Unique;

class UserController extends Controller
{
    use FormBuilderTrait;

    // index 
    public function index()
    {
        $form = $this->form(UserSeekForm::class, [
            'method' => 'POST',
            'url' => route('user.seek')
        ]);

        $records = User::leftJoin('config as g', 'users.gender', '=', 'g.id')
                    ->leftJoin('config as t', 'users.user_type', '=', 't.id')
                    ->leftJoin('branches', 'users.branch', '=', 'branches.id')
                    ->select('users.*', 'g.text as gender_text', 't.text as user_type_text', 'branches.text as branch_text')
                    ->where(function ($query) { 
                            // // admin
                            // if(!$this->tap->realRoot()) $query->where('staff.id', '>', 1);
                            // if(!$this->tap->isAdmin()) {
                            //     $query->where('staff.hide', false);
                            //     $query->whereIn('staff.department', $this->tap->allVisibleDepartments());
                            // }
                            if(Session::has('user_seek_array') && array_has(Session::get('user_seek_array'), 'branch') && Session::get('user_seek_array')['branch'] != '') {
                                $query->Where('users.branch', Session::get('user_seek_array')['branch']);
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
                        })
                    ->paginate(30);

        return view('users.index', compact('form'))->with('records', $records);
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

    public function logout()
    {
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
        return redirect('/');
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
        $all['password'] = bcrypt($all['work_id'].$all['mobile']);

        $id = User::create($all);
        return redirect('/user/'.$id->id);
    }

    // 个人
    public function show($id=0)
    {
        if($id===0) return redirect('/user');
        $record = Customer::find($id);

        if(!$record){
            $error = new Error;
            return $error->notFound();
        }

        return view('users.show');
    }

    // end
}




