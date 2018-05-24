<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Excel;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\UserImportForm;
use App\Helpers\Validator;
use App\Helpers\Unique;
use App\Helpers\Error;

use App\User;
use App\Config;

class ImportController extends Controller
{
    use FormBuilderTrait;

    public function index()
    {
        return $this->userImport();
    }

    // 新学员表单
    public function userImport()
    {
        $form = $this->form(UserImportForm::class, [
            'method' => 'POST',
            'url' => route('import.user_store')
        ]);

        $title = 'Excel导入成员';
        $icon = 'import';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储校验
    public function userStore(Request $request)
    {
        $resaults = Excel::load($request->file, function($reader) {
            // 
        })->get();

        $form = $this->form(UserImportForm::class);
        $validate = new Validator;
        $unique = new Unique;

        // print_r($array);
        $errors = [];
        $ok = [];

        $ok_list = [];
        $ignore_list = [];
        $mobile_list = [];

        for ($i=0; $i < count($resaults); $i++) { 
            $item = $resaults[$i];
            if(!$validate->checkMobile($item['mobile'])) {
                $error = '第'.($i+2).'行: '.$resaults[$i]['name'].'-'.$item['mobile']."手机号错误!";
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            // 默认员工类型
            $default_user_type_id = 50; // 其他
            $default_auth_type_id = 7; // 员工

            $user_type_id = Config::where('text', $item['user_type'])->first();

            $item_to_import = [
                'name'=>preg_replace('# #', '', $item['name']), 
                'mobile'=>$item['mobile'], 
                'branch'=>$request->branch,
                'gender'=>$item['gender'] == '女' ? 2 : 1,
                'user_type'=> $user_type_id ? $user_type_id->id : $default_user_type_id,
                'auth_type'=>$default_auth_type_id,
                'created_by'=>Session::get('id'),
            ];

            // 数据库中已存在手机号
            if($unique->exists($item['mobile'], 'mobile', 'users')){
                array_push($ignore_list, $item_to_import);
            }else{
                array_push($ok_list, $item_to_import);
            }
            // 获取手机号列表
            array_push($mobile_list, $resaults[$i]['mobile']);
        }

        $unique_mobile_list = array_unique($mobile_list);
        $diff = array_diff_assoc($mobile_list, $unique_mobile_list);

        // 提交文件中手机号重复
        if(count($diff)){
            $error = '文件中手机号: '.implode(',', $diff)."重复出现!";
            return redirect()->back()->withErrors(['file'=>$error])->withInput();
        }

        Session::put('ok_list', $ok_list);

        return view('users.import_info')
                    ->with('ok_list', $ok_list)
                    ->with('ignore_list', $ignore_list);


    }

    // 存入数据库
    public function userSave()
    {
        $error = new Error;
        $validate = new Validator;
        if(!Session::has('ok_list') || !count(Session::get('ok_list'))) return $error->paramLost();

        $ok_list = Session::get('ok_list');

        $next = $validate->getWorkId();

        for ($i=0; $i < count($ok_list); $i++) { 
            $ok_list[$i]['work_id'] = $next;
            $ok_list[$i]['password'] = bcrypt($next.$next);
            $next = $validate->nextWorkId($next);
        }

        User::insert($ok_list);
        if(Session::has('ok_list')) Session::forget('ok_list');
        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'Excel导入已成功!']);
    }

    // end
}
















