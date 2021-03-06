<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Excel;
use DB;
use Carbon\Carbon;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\UserImportForm;
use App\Forms\ClassImportForm;
use App\Forms\StepForm;

use App\Helpers\Validator;
use App\Helpers\Unique;
use App\Helpers\Error;
use App\Helpers\Auth;
use App\Helpers\Logs;

use App\User;
use App\Customer;
use App\Config;
use App\Classes;
use App\Biz;

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
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

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
         // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

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

            // 检测字段存在
            if(!isset($item['name']) || !isset($item['mobile']) || !isset($item['gender']) || !isset($item['user_type'])){
                $error = '文件格式错误';
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }
            // 空记录中止
            if($item['mobile'] == '') break;

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
         // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();
        
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
        if(count($ok_list)) {
            foreach ($ok_list as $key) {
                User::create($key);
            }
        }

        // 日志
        $log_content = "批量导入: 成员,共".count($ok_list).'项';
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        if(Session::has('ok_list')) Session::forget('ok_list');
        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'Excel导入已成功!']);
    }

    // 开班花名册
    public function classImport()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $form = $this->form(ClassImportForm::class, [
            'method' => 'POST',
            'url' => route('import.class_store')
        ]);
        $title = 'Excel导入: 开班花名册';
        $icon = 'import';
        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 校验
    public function classStore(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $resaults = Excel::load($request->file, function($reader) {
            // 
        })->get();

        $form = $this->form(ClassImportForm::class);
        $validate = new Validator;
        $unique = new Unique;

        // print_r($array);
        $all_id_number_list = [];
        $new_customer_list = [];
        $update_customer_list = [];

        // 驾校列表
        $branches_list = DB::table('branches')
                            ->where('id', '>', 1)
                            ->where('show', true)
                            ->select(['id', 'text'])
                            ->get()
                            ->toArray();
                            
        if(!count($branches_list)) return $auth_error->paramLost();

        for ($i=0; $i < count($resaults); $i++) { 

            $item = $resaults[$i];
            
            // 检测字段存在
            if(!isset($item['name']) || !isset($item['mobile']) || !isset($item['id_number']) || !isset($item['address']) || !isset($item['gender'])){
                $error = '文件格式错误';
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }
            // 空记录中止
            if($item['id_number'] == '') break;

            if(!$validate->checkMobile($item['mobile'])) {
                $error = '第'.($i+2).'行: '.$resaults[$i]['name'].'-'.$item['mobile']."手机号错误!";
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }
            if(!$validate->checkIdNumber($item['id_number'])) {
                $error = '第'.($i+2).'行: '.$resaults[$i]['name'].'-'.$item['id_number']."身份证号错误!";
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            // 驾校
            $branch_text_import = trim($item['content']);
            $branch_import = 1;

            if(isset($item['content']) && $branch_text_import != '' && $branch_text_import != null){

                // 存在
                foreach ($branches_list as $key) {
                    if(str_contains($key->text, $branch_text_import)) {
                        $branch_import = $key->id;
                    }
                }

                // 不存在
                if($branch_import == 1) {
                    $error = '第'.($i+2).'行: '."驾校名称错误!";
                    return redirect()->back()->withErrors(['file'=>$error])->withInput();
                }
            }


            $item_to_import = [
                'name'=>preg_replace('# #', '', $item['name']), 
                'mobile'=>$item['mobile'], 
                'id_number'=>strtoupper($item['id_number']),
                'address'=>$item['address'],
                'gender'=>$item['gender'] == '女' ? 2 : 1,
                'created_by'=>Session::get('id'),
                // 'branch'=>$branch_import,
                // 'created_at'=>time(),
                // 'date'=>time(),
            ];

            // 如果数据库中无记录, 则新建
            if(!$unique->exists_id_number($item['id_number'], 'id_number', 'customers')){
                array_push($new_customer_list, $item_to_import);
            }else{
                array_push($update_customer_list, $item_to_import);
            }

            // 所有开班人员
            // array_push($all_id_number_list, $item['id_number']);
            $all_id_number_list = array_add($all_id_number_list, $item['id_number'], $item['licence_type'].','.$branch_import);
        }

        $unique_all_id_number_list = array_unique($all_id_number_list);
        $diff = array_diff_assoc($unique_all_id_number_list, $all_id_number_list);

        // 提交文件中身份证重复
        if(count($diff)){
            $error = '文件中身份证号: '.implode(',', $diff)."重复出现!";
            return redirect()->back()->withErrors(['file'=>$error])->withInput();
        }

        $class_info = ['branch'=>$request->branch, 'date'=>$request->date, 'class_no'=>$request->class_no];
        Session::put('new_customer_list', $new_customer_list);
        Session::put('all_id_number_list', $all_id_number_list);
        Session::put('update_customer_list', $update_customer_list);
        Session::put('class_info', $class_info);



        return view('classes.import_info')
                    ->with('all', count($all_id_number_list))
                    ->with('new', count($new_customer_list));
 
    }

    // 写入开班信息
    public function classSave()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $error = new Error;
        $validate = new Validator;
        if(!Session::has('new_customer_list')|| !Session::has('all_id_number_list') || !count(Session::get('all_id_number_list'))) return $error->paramLost();

        $new_customer_list = Session::get('new_customer_list');
        $all_id_number_list = Session::get('all_id_number_list');
        $update_customer_list = Session::get('update_customer_list');
        $class_info = Session::get('class_info');

        if(Session::has('new_customer_list')) Session::forget('new_customer_list');
        if(Session::has('all_id_number_list')) Session::forget('all_id_number_list');
        if(Session::has('update_customer_list')) Session::forget('update_customer_list');
        if(Session::has('class_info')) Session::forget('class_info');

        if(count($new_customer_list)) {
            foreach ($new_customer_list as $key) {
                Customer::create($key);
            }
        }

        // 更新客户地址和姓名字段
        if(count($update_customer_list)){
            foreach ($update_customer_list as $key) {
                $customer = Customer::where('id_number', $key['id_number'])
                                    ->first()
                                    ->update(['name'=>$key['name'], 'address'=>$key['address']]);
            }
        } 

        // print_r($class_info);

        $has = Classes::where('branch', $class_info['branch'])->where('class_no', $class_info['class_no'])->first();
        $class_id = 0;

        if($has){
            $class_id = $has->id;
        }else{
            $class_id = Classes::create($class_info)->id;
        }

        foreach ($all_id_number_list as $id_number => $mix) {
            // 分解混合值
            $tmp = explode(',', $mix);
            $licence_type = $tmp[0];
            $branch = $tmp[1];

            $customer = Customer::where('id_number', $id_number)->first();
            $customer_id = $customer->id;

            $licence_type_id = Config::where('text', 'LIKE', $licence_type.":%")->first()->id;

            $default_class_type_id = 23;
            
            $has = Biz::where('customer_id', $customer_id)
                        ->where('licence_type', $licence_type_id)
                        ->where('finished', false)
                        ->first();

            // $customer = Customer::find()

            if(!$has){
                Biz::insert(['customer_id'=>$customer_id, 'licence_type'=>$licence_type_id, 'created_by'=>Session::get('id'), 'class_id'=>$class_id, 'class_type'=>$default_class_type_id, 'date'=>time(), 'branch'=>$branch]);
            }else{
                $has->update(['class_id'=>$class_id, 'licence_type'=>$licence_type_id]);
            }
        }

        // 日志
        $log_content = "批量导入: 开班花名册,共".count($all_id_number_list).'项';
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'Excel导入已成功!']);
    }

    // 学员进度
    public function step()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $form = $this->form(StepForm::class, [
            'method' => 'POST',
            'url' => '/import/step/store'
        ]);
        $title = 'Excel导入: 考试进度';
        $icon = 'import';
        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

       // 校验
    public function stepStore(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $resaults = Excel::load($request->file, function($reader) {
            // 
        })->get();

        $form = $this->form(StepForm::class);
        $validate = new Validator;
        // $unique = new Unique;

        // // print_r($array);
        // $all_id_number_list = [];
        // $new_customer_list = [];
        // $update_customer_list = [];

        // // 驾校列表
        // $branches_list = DB::table('branches')
        //                     ->where('id', '>', 1)
        //                     ->where('show', true)
        //                     ->select(['id', 'text'])
        //                     ->get()
        //                     ->toArray();
                            
        // if(!count($branches_list)) return $auth_error->paramLost();

        for ($i=0; $i < count($resaults); $i++) { 

            $item = $resaults[$i];
            
            // 检测字段存在
            if(!isset($item['name']) || !isset($item['id_number']) || !isset($item['licence_type']) || !isset($item['step']) || !isset($item['start_date']) ||!isset($item['out_date'])){
                $error = '文件格式错误';
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            // 空记录中止
            if($item['id_number'] == '') break;

            // 身份证
            if(!$validate->checkIdNumber($item['id_number'])) {
                $error = '第'.($i+2).'行: '.$resaults[$i]['name'].'-'.$item['id_number']."身份证号错误!";
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            // 次数
            if(!intval($item['step']) && $item['step'] != 0 && $item['step'] != '0' ) {
                $error = '第'.($i+2).'行: 历史考务必须为整数!';
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            if(intval($request->lesson) == 1) {
                // 科目1不得有日期区间
                if(strtotime($item['start_date']) == true || strtotime($item['start_date']) == true) {
                    $error = '第'.($i+2).'行: 科目1导入不应有考试有效期!';
                    return redirect()->back()->withErrors(['file'=>$error])->withInput();
                }

            }else{
                // 科目1通过后必须检测时间段
                // 检验日期: 如果日期不为空则检验,为空则不校验
                if($item['start_date'] != '' && $item['out_date'] != ''){
                    if(strtotime($item['start_date']) == false) {
                        $error = '第'.($i+2).'行: 考试有效期起点格式错误!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }

                    if(strtotime($item['out_date']) == false) {
                        $error = '第'.($i+2).'行: 考试有效期结束点格式错误!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }

                    if(Carbon::parse($item['start_date'])->addYears(3) < Carbon::today() || Carbon::parse($item['start_date']) > Carbon::today()) {
                        $error = '第'.($i+2).'行: 考试有效期起点非法!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }

                    // 检验日期: 结束点
                    if(Carbon::parse($item['out_date']) > Carbon::today()->addYears(3) || Carbon::parse($item['out_date']) < Carbon::today()) {
                        $error = '第'.($i+2).'行: 考试有效期结束点非法!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }
                }
            }

            // 检验准考证号
            if(intval($request->lesson) == 3) {
                // 
            }

            // 检验驾照类型字段
            $licence = DB::table('config')
                            ->where('type', 'licence_type')
                            ->where('text', 'LIKE', $item['licence_type'].':%');

            $licence_num = count($licence->get());

            if($licence_num == 0 || $licence_num > 1) {
                $error = '第'.($i+2).'行: 驾照类型错误!';
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            // 校验用户
            $target = DB::table('biz')
                            ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                            ->select('biz.branch', 'biz.id', 'biz.next', 'biz.next2', 'biz.next3')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.class_id')
                            ->where('customers.id_number', $item['id_number'])
                            ->where('biz.licence_type', $licence->first()->id);

            $target_num = count($target->get());

            if($target_num == 0 || $target_num > 1) {
                $error = '第'.($i+2).'行: 学员未登记，或者虽然登记但尚未开班!';
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            // 检验驾校归属
            if($target->first()->branch != $request->branch && $target->first()->branch != 1) {
                $error = '第'.($i+2).'行: 该学员不属于以上选择的驾校!';
                return redirect()->back()->withErrors(['file'=>$error])->withInput();
            }

            // 检验考试进度
            switch (intval($request->lesson)) {
                case 1:
                    if($target->first()->next2 != '2.0' || $target->first()->next3 != '3.0' || $target->first()->next == '4.0' || $target->first()->next == '4.1' || $target->first()->next == '4.2' || $target->first()->next == '4.3') {
                        $error = '第'.($i+2).'行: 该学员已有科目2/3/4考务记录,科目1记录已过期!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }

                    break;

                case 2:
                    if($target->first()->next == '4.0' || $target->first()->next == '4.1' || $target->first()->next == '4.2' || $target->first()->next == '4.3') {
                        $error = '第'.($i+2).'行: 该学员已有科目4考务记录,科目2记录已过期!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }

                    break;

                case 3:
                    if($target->first()->next == '4.0' || $target->first()->next == '4.1' || $target->first()->next == '4.2' || $target->first()->next == '4.3') {
                        $error = '第'.($i+2).'行: 该学员已有科目4考务记录,科目3记录已过期!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }

                    break;

                case 4:
                    if($target->first()->next == '4.3') {
                        $error = '第'.($i+2).'行: 该学员科目4已通过,数据已已过期!';
                        return redirect()->back()->withErrors(['file'=>$error])->withInput();
                    }

                    break;
                
                default:
                    # code...
                    break;
            }

        }
        // 缓存结果
        session(['step_imports' => $resaults]);
        session(['step_imports_requests' => ['lesson' => $request->lesson, 'branch' => $request->branch]]);

        $all = count($resaults);
        return view('import.step_info', compact('all'));
 
    }

    // 写入
    public function stepSave()
    {
        // 授权
        $auth = new Auth;
        $error = new Error;
        if(!$auth->admin())  return $error->forbidden();

        if(!Session::has('step_imports') || count(session('step_imports')) == 0 || !Session::has('step_imports_requests')) return $error->paramLost();

        
        $resaults = session('step_imports');
        $requests = session('step_imports_requests');

        for ($i=0; $i < count($resaults); $i++) { 

            $item = $resaults[$i];

            $licence = DB::table('config')
                            ->where('type', 'licence_type')
                            ->where('text', 'LIKE', $item['licence_type'].':%')
                            ->first()->id;

            $target = DB::table('biz')
                            ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                            ->select('biz.branch', 'biz.id', 'biz.next', 'biz.next2', 'biz.next3', 'biz.content')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.class_id')
                            ->where('customers.id_number', $item['id_number'])
                            ->where('biz.licence_type', $licence)
                            ->first();

            // 分类
            $branch = $requests['branch'];

            $new_content = intval($item['step']) == 0 ? null : $item['step'];
            $content = $new_content ? '科目'.$requests['lesson'].'补考'.$new_content.'次; '.$target->content : $target->content;

            $start_date = Carbon::parse($item['start_date']);
            $out_date = Carbon::parse($item['out_date']);

            switch ($requests['lesson']) {
                case 1:
                    Biz::find($target->id)->update(['branch' => $branch, 'next' => '1.0', 'next2' => '2.0', 'next3' => '3.0', 'content' => $content]);
                    // $target->update(['branch' => $branch, 'next' => '1.0', 'next2' => '2.0', 'next3' => '3.0', 'content' => $content]);
                    break;

                case 2:
                    Biz::find($target->id)->update(['branch' => $branch, 'next' => '1.3', 'next2' => '2.0', 'start_date'=>$start_date, 'out_date'=>$out_date, 'content' => $content]);
                    break;

                case 3:
                    Biz::find($target->id)->update(['branch' => $branch, 'next' => '1.3', 'next3' => '3.0', 'start_date'=>$start_date, 'out_date'=>$out_date, 'content' => $content]);
                    break;

                case 4:
                    Biz::find($target->id)->update(['branch' => $branch, 'next' => '4.0', 'next2' => '2.3', 'next3' => '3.3', 'start_date'=>$start_date, 'out_date'=>$out_date, 'content' => $content]);
                    break;
                
                default:
                    # code...
                    break;
            }

            // 清理session
            if(Session::has('step_imports')) Session::forget('step_imports');
            if(Session::has('step_imports_requests')) Session::forget('step_imports_requests');
        }

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'Excel导入已成功!']);
    }


    // end
}
















