<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use App\Customer;
use App\Biz;
use App\Finance;
use DB;
use Excel;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\CustomerForm;
use App\Forms\CustomerSeekForm;

use App\Helpers\Validator;
use App\Helpers\Error;
use App\Helpers\Unique;
use App\Helpers\Auth;
use App\Helpers\Pre;

class CustomerController extends Controller
{
    use FormBuilderTrait;

    private $auth;

    private function prepare() 
    {
        // 更新预处理财务结果数据
        $pre = new Pre;
        $pre->updateFinance();

        $this->auth = new Auth;

        $records = DB::table('customers')
            ->leftJoin('config', 'customers.gender', '=', 'config.id')
            ->leftJoin('users', 'customers.created_by', '=', 'users.id')
            ->leftJoin('biz', 'customers.id', '=', 'biz.customer_id')
            ->leftJoin('config as bc', 'biz.licence_type', '=', 'bc.id')
            // ->leftJoin('finance', 'customers.id', '=', 'finance.customer_id')
            ->leftJoin('branches', 'biz.branch', '=', 'branches.id')
            ->select('customers.*',
                     'config.text as gender_text', 
                     'users.name as created_by_text',
                     DB::raw('
                        group_concat(biz.branch) as biz_branch, 
                        group_concat(biz.user_id) as biz_user_id, 
                        group_concat(biz.licence_type) as licence_type, 
                        group_concat(bc.text) as licence_type_text, 
                        group_concat(branches.text) as biz_branch_text
                        '))
            ->where(function ($query) { 
                // 关键词
                if(Session::has('seek_array') && array_has(Session::get('seek_array'), 'key') && Session::get('seek_array')['key'] != '') {
                    $query->Where('customers.name', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                    $query->orWhere('customers.mobile', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                    $query->orWhere('customers.id_number', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                    // $query->orWhere('customers.finance_info', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                    $query->orWhere('customers.address', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                    $query->orWhere('customers.location', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                    $query->orWhere('customers.content', 'LIKE', '%'.Session::get('seek_array')['key'].'%');
                }

                // 分支机构限制
                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                    $query->where('biz.branch', $this->auth->branchLimitId());
                    // $query->where('biz.finished', false);
                    // $query->orWhere('finance.branch', '=', $this->auth->branchLimitId());
                    if(!Session::has('seek_array')) $query->orWhere('biz.branch', 1); # 认领
                }
                
            });
        return $records;
    }

    // index 
    public function index()
    {

        $form = $this->form(CustomerSeekForm::class, [
            'method' => 'POST',
            'url' => route('customer.seek')
        ]);

        $records = $this->prepare()
                        ->groupBy('customers.id')
                        ->orderBy('biz.branch')
                        // ->orderBy('customers.finance_info', 'desc')
                        ->orderBy('customers.created_at', 'desc')
                        ->paginate(50);
        // Session::put('customer_list', $records);

        return view('customers.index', compact('form'))->with('records', $records);
    }

    // 查询条件
    public function seek(Request $request)
    {
        $seek_array = [];
        if($request->has('key') && trim($request->key) != ''){
            $seek_array = array_add($seek_array, 'key', trim($request->key));
            Session::put('seek_array', $seek_array);
            return redirect('/customer');
        } 
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

        $all['created_by'] = Session::get('id');
        $all['id_number'] = strtoupper($all['id_number']);

        $id = Customer::create($all);
        return redirect('/customer/'.$id->id);
    }

    // 个人
    public function show($id=0)
    {
        if($id===0) return redirect('/customer');
        $record = Customer::leftJoin('config', 'customers.gender', '=', 'config.id')
                          ->leftJoin('users', 'customers.created_by', '=', 'users.id')
                          ->select('customers.*', 'config.text as gender_text', 'users.name as created_by_text')
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
                    ->leftJoin('users as u', 'biz.user_id', '=', 'u.id')
                    ->leftJoin('branches', 'biz.branch', '=', 'branches.id')
                    ->leftJoin('classes', 'biz.class_id', '=', 'classes.id')
                    ->leftJoin('branches as bb', 'classes.branch', '=', 'bb.id')
                    ->leftJoin('lessons', 'biz.id', '=', 'lessons.biz_id')
                    ->select('biz.*',
                            'customers.name as customer_name', 
                            'lt.text as licence_type_text', 
                            'ct.text as class_type_text', 
                            'users.name as created_by_text', 
                            'branches.text as branch_text', 
                            'classes.class_no as class_no', 
                            'bb.text as class_branch_text', 
                            'u.name as user_id_text',
                            DB::raw('
                                group_concat(lessons.lesson) as lesson, 
                                group_concat(lessons.ready) as lesson_ready, 
                                group_concat(lessons.order_date) as lesson_order_date, 
                                group_concat(lessons.pass) as lesson_pass, 
                                group_concat(lessons.doing) as lesson_doing, 
                                group_concat(lessons.end) as lesson_end
                                    ')
                        )
                    // ->orderBy('lessons.pass')
                    ->groupBy('biz.id')
                    ->orderByRaw('lessons.lesson - lessons.lesson DESC')
                    ->orderBy('lessons.lesson', 'desc')
                    // ->orderBy('lessons.lesson')
                    ->get();

        $finance = DB::table('finance')
                            ->where('finance.customer_id', $id)
                            ->leftJoin('config', 'finance.item', '=', 'config.id')
                            ->leftJoin('customers', 'finance.customer_id', '=', 'customers.id')
                            ->leftJoin('users as c', 'finance.created_by', '=', 'c.id')
                            ->leftJoin('users as a', 'finance.user_id', '=', 'a.id')
                            ->leftJoin('branches', 'finance.branch', '=', 'branches.id')
                            ->select('finance.*', 'config.text as item_text', 'customers.name as customer_name', 'c.name as created_by_text', 'a.name as user_id_text', 'branches.text as branch_text')
                            ->orderBy('finance.created_at', 'desc')
                            ->orderBy('finance.updated_at', 'desc')
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

    // 修改信息
    public function edit($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $record = Customer::find($id);

        $form = $this->form(CustomerForm::class, [
            'method' => 'POST',
            'model' => $record,
            //'url' => route('fashion.update')
            'url' => '/customer/update/'.$id
        ]);

        return view('form', compact('form'))->with('custom',['title'=>'信息修改 - '.$record->name, 'icon'=>'cog']);
    }

    // 执行修改
    public function update(Request $request, $id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();
        
        $all = $request->all();
        $form = $this->form(CustomerForm::class);

        $validate = new Validator;
        // 身份证格式检查
        if(!$validate->checkIdNumber($all['id_number'])) return redirect()->back()->withErrors(['id_number'=>'身份证错误!'])->withInput();
        // 身份证唯一性检查
        $exists = new Unique;
        $resault = $exists->exists($all['id_number'], 'id_number', 'customers');
        $target = Customer::find($id);
        // $id_number = Customer::find()
        if($resault && $target->id_number != $all['id_number']) {
            $message = "此身份证号已存在! <a href=\"/customer/".$resault."\">若确认输入无误, 可转到已有记录</a>";
            return redirect()->back()->withErrors(['id_number'=>$message])->withInput();
        }
        // 手机号格式检查
        if(!$validate->checkMobile($all['mobile'])) return redirect()->back()->withErrors(['mobile'=>'手机号错误!'])->withInput();

        $target->update($all);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'学员信息修改成功!']);
    }

    // 导入开班花名册
    public function importClass()
    {
        # code...
    }

    // 输出Execl
    public function seekToExcel()
    {
        $cellData = [
            ['姓名', '电话', '学身份证', '财务统计', '业务统计', '身份证地址', '现居地'],
        ];

        $records = $this->prepare()
                        ->orderBy('biz.branch')
                        // ->orderBy('customers.finance_info', 'desc')
                        ->orderBy('customers.created_at', 'desc')
                        ->groupBy('customers.id')
                        // ->orderBy('finance.date')
                        ->get();

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        $record->name,
                                        $record->mobile, 
                                        '#'.$record->id_number, 
                                        $record->finance_info, 
                                        $record->biz_info,
                                        $record->address,
                                        $record->location
                                    ]);
            }
        }
        $file_name = '学员'.date('Y-m-d', time());

        Excel::create($file_name,function($excel) use ($cellData){
            $excel->sheet('列表', function($sheet) use ($cellData){
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

    // end
}




