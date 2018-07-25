<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Excel;
use Input;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\FinanceSeekForm;
use App\Forms\FinanceForm;
use App\Forms\FinanceEditForm;
use App\Finance;
use App\Customer;
use App\User;
use Carbon\Carbon;

use App\Helpers\Error;
use App\Helpers\ConfigList;
use App\Helpers\Auth;
use App\Helpers\Logs;
use App\Helpers\Pre;

class FinanceController extends Controller
{
    use FormBuilderTrait;

    private $auth;

    private function prepare() 
    {
        $this->auth = new Auth;

        $records = Finance::leftJoin('config as i', 'finance.item', '=', 'i.id')
                          ->leftJoin('customers', 'finance.customer_id', '=', 'customers.id')
                          ->leftJoin('users as c', 'finance.created_by', '=', 'c.id')
                          ->leftJoin('users as ck', 'finance.checked_by', '=', 'ck.id')
                          ->leftJoin('users as ck2', 'finance.checked_2_by', '=', 'ck2.id')
                          ->leftJoin('users as u', 'finance.user_id', '=', 'u.id')
                          ->leftJoin('branches', 'finance.branch', '=', 'branches.id')
                          ->leftJoin('biz', 'finance.biz_id', '=', 'biz.id')
                          ->leftJoin('config as ib', 'biz.licence_type', '=', 'ib.id')
                          ->select(
                                'biz.customer_id as biz_customer_id',
                                'finance.*', 
                                'i.text as item_text', 
                                'customers.name as customer_id_text', 
                                'customers.id_number as customer_id_number', 
                                'c.name as created_by_text', 
                                'u.name as user_id_text', 
                                'branches.text as branch_text', 
                                'customers.mobile as customer_mobile', 
                                'ck.name as checked_by_text', 
                                'ck2.name as checked_2_by_name',
                                'ib.text as licence_type_text'
                            )
                          ->where(function ($query) {
                                if(Session::has('finance_date_start')){
                                    $query->where('finance.date', '>=', strtotime(session('finance_date_start')));
                                }

                                if(Session::has('finance_date_end')){
                                    $end_of_day = Carbon::parse(session('finance_date_end'))->endOfDay();
                                    $query->where('finance.date', '<=', strtotime($end_of_day));
                                }

                                if(Session::has('finance_key')){
                                    $query->Where('finance.price', 'LIKE', '%'.session('finance_key').'%');
                                    $query->orWhere('finance.real_price', 'LIKE', '%'.session('finance_key').'%');
                                    $query->orWhere('customers.name', 'LIKE', '%'.session('finance_key').'%');
                                    $query->orWhere('c.name', 'LIKE', '%'.session('finance_key').'%');
                                    $query->orWhere('u.name', 'LIKE', '%'.session('finance_key').'%');
                                    $query->orWhere('i.text', 'LIKE', '%'.session('finance_key').'%');
                                }

                                // 分支机构限制
                                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                    $query->Where('finance.branch', $this->auth->branchLimitId());
                                }

                            });

        return $records;
    }

    // index 
    public function index()
    {

        // 查询预处理
        $records = $this->prepare()
            ->latest('finance.date')
            ->latest('finance.updated_at')
            ->latest('finance.created_at')
            ->paginate(50);

        $all = $this->prepare()->get()->count();

        return view('finance.main', compact('records', 'all'));
    }

        // 查询条件
    public function seek(Request $request)
    {
        $all = $request->all();

        if($request->date_start != '') {
            Session::put('finance_date_start', $request->date_start);
        }else{
            if(Session::has('finance_date_start')) Session::forget('finance_date_start');
        }

        if($request->date_end != '') {
            Session::put('finance_date_end', $request->date_end);
        }else{
            if(Session::has('finance_date_end')) Session::forget('finance_date_end');
        }

        if($request->key != '') {
            Session::put('finance_key', $request->key);
        }else{
            if(Session::has('finance_key')) Session::forget('finance_key');
        }

        return redirect('/finance');

    }

    // 查询重置
    public function seekReset()
    {
        if(Session::has('finance_date_start')) Session::forget('finance_date_start');
        if(Session::has('finance_date_end')) Session::forget('finance_date_end');
        if(Session::has('finance_key')) Session::forget('finance_key');

        return redirect('/finance');
    }

    // 新收费
    public function create($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if($auth->admin() ||  !$auth->hasBiz($id))  return $auth_error->forbidden();

        $record = Customer::find($id);
        $error = new Error;
        if(!$record) return $error->notFound();

        $form = $this->form(FinanceForm::class, [
            'method' => 'POST',
            // 'url' => route('finance.store')
            'url' => '/finance/store/'.$id
        ]);

        $title = '收付款 - '.$record->name;
        $icon = 'credit-card';

        return view('form_with_selector', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储
    public function store(Request $request, $id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if($auth->admin() ||  !$auth->hasBiz($id))  return $auth_error->forbidden();
        
        $all = $request->all();
        $form = $this->form(FinanceForm::class);

        // 推荐人
        if($all['user_id'] != '' || $all['user_id'] != null){
            $exists = User::where('work_id', $all['user_id'])->orWhere('mobile', $all['user_id'])->first();
            if(!$exists) {
                $message = "工号或手机号不存在!";
                return redirect()->back()->withErrors(['user_id'=>$message])->withInput();
            }else{
                $all['user_id'] = $exists->id;

                // 若有推荐人, 则财务记录归属推荐人所在机构
                // $all['branch'] = $exists->branch;
            }
        }

        $all['in'] =  $all['in'] == 1 ? true :false; 

        $all['customer_id'] = $id;
        $all['created_by'] = Session::get('id');
        $all['date'] = time();
        $all['checked'] = true;
        $all['checked_by'] = Session::get('id');
        $all['checked_by_time'] = time();
        // $all['date'] = strtotime($all['date']);

        $id = Finance::create($all);
        // 日志
        $log_content = "财务: 收付费, 序号:".$id->id;
        $log_level = "info";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        // 更新预处理财务结果数据
        $pre = new Pre;
        $pre->updateFinance();

        return redirect('/customer/'.$all['customer_id']);
    }

    // 修改
    public function edit($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;

        $record = Finance::leftJoin('users', 'finance.user_id', '=', 'users.id')
                        ->select(
                            'finance.*', 
                            'users.name as user_name', 
                            'users.work_id as user_id'
                        )
                        ->find($id);

        $form = $this->form(FinanceEditForm::class, [
            'method' => 'POST',
            'model' => $record,
            'url' => '/finance/update/'.$id
        ]);


        if($auth->admin() && !$record->abandon && !$record->checked_2){
            $title = ' 修改财务记录, 推荐人 - '.($record->user_id ? $record->user_name : '无').' - '.date('Y-m-d', $record->date);
            $icon = 'warning-sign';

            return view('form_with_selector', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
        }else{
           return $auth_error->forbidden(); 
        } 
    }

    // 修改
    public function update(Request $request, $id)
    {
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $error = new Error;

        $all = $request->all();
        $user = User::where('work_id', $all['user_id'])->get();

        if(count($user) != 1) return $error->paramLost();
        $all['user_id'] = User::where('work_id', $all['user_id'])->first()->id;

        if($all['date'] == '' || $all['date'] == null) {
            array_forget($all, 'date');
        }else{
            $all['date'] = strtotime($all['date']);
        }

        $all['in'] =  $all['in'] == 1 ? true : false; 

        // print_r($all);

        Finance::find($id)->update($all);
        // 日志
        $log_content = "财务: 修改(审核前), 序号:".$id;
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        // 更新预处理财务结果数据
        $pre = new Pre;
        $pre->updateFinance();

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'财务信息修改成功!']);

    }

    // 登记单据
    public function checking(Request $request)
    {
        $ticket_no = $request->input('no');
        $id = $request->input('id');
        // 授权
        $auth = new Auth;
        // if(!$auth->user() && !$auth->finance())  return "无权操作";
        if(!$auth->user())  return "无权操作";

        $target = Finance::find($id)->update(['checked' => true, 'checked_by'=>Session::get('id'), 'checked_by_time'=>time(), 'ticket_no'=>$ticket_no]);
        // return redirect('/finance');
        // echo 'fuck';
        return '票号:'.$ticket_no.'已成功登记!';
    }

    // 审核单据
    public function check_2 (Request $request)
    {
        $id = $request->input('id');

        // 授权
        $auth = new Auth;
        if(!$auth->financeMaster())  return "无权操作";

        $target = Finance::find($id);
        if(!$target->checked || !$target->checked_by || !$target->ticket_no) return "记录单据登记异常!";

        $target->update(['checked_2' => true, 'checked_2_by'=>Session::get('id'), 'checked_2_by_time'=>time()]); 

        // 日志
        $log_content = "财务: 审核通过票据, 序号:".$id;
        $log_level = "info";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return "审核成功!!".date('Y-m-d h:m:s', $target->checked_2_by_time);
    }

    // 撤销单据
    public function cancel (Request $request)
    {
        // 授权
        $auth = new Auth;
        if(!$auth->admin())  return "无权操作";

        $id = $request->input('id');
        // if(!$target->checked || !$target->checked_by || !$target->ticket_no) return "记录单据登记异常!";

        $target = Finance::find($id);
        $target = Finance::find($id)->update(['checked' => false, 'checked_by'=>null, 'checked_by_time'=>null, 'ticket_no'=>null]);

        // 日志
        $log_content = "财务: 撤销单据, 序号:".$id;
        $log_level = "warning";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return "已撤销!!";
    }

     // 废弃单据
    public function abandon (Request $request)
    {
        // 授权
        $auth = new Auth;
        if(!$auth->root())  return "无权操作";

        $id = $request->input('id');
        $reason = $request->input('reason');
        // if(!$target->checked || !$target->checked_by || !$target->ticket_no) return "记录单据登记异常!";

        // $target = Finance::find($id);
        $target = Finance::find($id)->update(['abandon' => true, 'content'=>$reason]);

        // 更新预处理财务结果数据
        $pre = new Pre;
        $pre->updateFinance();

        // 日志
        $log_content = "财务: 废弃单据, 序号:".$id;
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return "已废弃!!";
    }

    // 输出Execl
    public function seekToExcel()
    {
        $cellData = [
            ['收付', '驾校', '学员', '身份证', '学员电话', '项目', '应收/付', '实收付', '日期', '经手人', '推荐人', '票据号', '业务车型'],
        ];

        $records = $this->prepare()
                ->where('abandon', false)
                ->orderBy('finance.date')
                ->get();

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        $record->in ? '收' : '付', 
                                        $record->branch_text, 
                                        $record->customer_id_text, 
                                        $record->customer_id_number.' ', 
                                        $record->customer_mobile, 
                                        $record->item_text, 
                                        $record->price,$record->real_price,
                                        date('Y-m-d', $record->date),
                                        $record->created_by_text,
                                        $record->user_id_text,
                                        $record->ticket_no,
                                        $record->licence_type_text,
                                    ]);
            }
        }
        $file_name = '财务'.date('Y-m-d', time());

        // 日志
        $log_content = "财务: 下载财务报表(可能为查询结果)";
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        Excel::create($file_name,function($excel) use ($cellData){
            $excel->sheet('列表', function($sheet) use ($cellData){
                // $sheet->getStyle('D')->getNumberFormat()->setFormatCode('FORMAT_TEXT');
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

    // end
}



















