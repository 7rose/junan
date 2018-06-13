<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Excel;
use Input;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\FinanceSeekForm;
use App\Forms\FinanceForm;
use App\Finance;
use App\Customer;
use App\User;

use App\Helpers\Error;
use App\Helpers\ConfigList;
use App\Helpers\Auth;

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
                          ->select('finance.*', 'i.text as item_text', 'customers.name as customer_id_text', 'c.name as created_by_text', 'u.name as user_id_text', 'branches.text as branch_text', 'customers.mobile as customer_mobile', 'ck.name as checked_by_text', 'ck2.name as checked_2_by_name')
                          ->where(function ($query) {
                                // 分支机构限制
                                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                    $query->Where('finance.branch', $this->auth->branchLimitId());
                                }
                                 
                                // 起时间点
                                if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_begin') && Session::get('finance_seek_array')['date_begin'] != '') {
                                    $query->Where('finance.date', '>=', strtotime(Session::get('finance_seek_array')['date_begin']));
                                }

                                // 终时间点
                                if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_end') && Session::get('finance_seek_array')['date_end'] != '') {
                                    $query->Where('finance.date', '<=', strtotime(Session::get('finance_seek_array')['date_end']));
                                }

                                // 关键词
                                if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'key') && Session::get('finance_seek_array')['key'] != '') {
                                    $query->Where('finance.price', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('finance.real_price', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('customers.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('c.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('u.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('i.text', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                }
                            });
                          // ->orderBy('finance.created_by', 'desc')
                          // ->orderBy('finance.date', 'desc')
        return $records;
    }

    // index 
    public function index()
    {
        $form = $this->form(FinanceSeekForm::class, [
            'method' => 'POST',
            'url' => route('finance.seek')
        ]);

        // 查询预处理
        $records = $this->prepare()
            ->orderBy('finance.date', 'desc')
            ->orderBy('finance.created_by', 'desc')
            ->paginate(30);

        return view('finance.index', compact('form'))->with('records', $records);
    }

        // 查询条件
    public function seek(Request $request)
    {
        $finance_seek_array = [];
        if($request->has('key') && trim($request->key) != '') $finance_seek_array = array_add($finance_seek_array, 'key', trim($request->key));
        if($request->has('branch')) $finance_seek_array = array_add($finance_seek_array, 'branch', $request->branch);
        if($request->has('date_begin')) $finance_seek_array = array_add($finance_seek_array, 'date_begin', $request->date_begin);
        if($request->has('date_end')) $finance_seek_array = array_add($finance_seek_array, 'date_end', $request->date_end);
        Session::put('finance_seek_array', $finance_seek_array);
        return redirect('/finance');
    }

    // 查询重置
    public function seekReset()
    {
        if(Session::has('finance_seek_array')) Session::forget('finance_seek_array');
        return redirect('/finance');
    }

    // 新收费
    public function create($id)
    {
        $record = Customer::find($id);
        $error = new Error;

        if(!$record) return $error->notFound();

        $form = $this->form(FinanceForm::class, [
            'method' => 'POST',
            'url' => route('finance.store')
        ]);

        $title = '收付款 - '.$record->name;
        $icon = 'credit-card';

        return view('form_with_selector', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储
    public function store(Request $request)
    {
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
            }
        }

        $all['in'] =  $all['in'] == 1 ? true :false; 

        $all['created_by'] = Session::get('id');
        $all['date'] = time();
        $all['checked'] = true;
        $all['checked_by'] = Session::get('id');
        $all['checked_by_time'] = time();
        // $all['date'] = strtotime($all['date']);

        $id = Finance::create($all);
        return redirect('/customer/'.$all['customer_id']);
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

        return "已撤销!!";
    }

     // 废弃单据
    public function abandon (Request $request)
    {
        // 授权
        $auth = new Auth;
        if(!$auth->root())  return "无权操作";

        $id = $request->input('id');
        // if(!$target->checked || !$target->checked_by || !$target->ticket_no) return "记录单据登记异常!";

        $target = Finance::find($id);
        $target = Finance::find($id)->update(['abandon' => true]);

        return "已废弃!!";
    }

    // 输出Execl
    public function seekToExcel()
    {
        $cellData = [
            ['收付', '驾校', '学员', '学员电话', '项目', '应收/付', '实收付', '日期', '经手人', '推荐人'],
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
                                        $record->customer_mobile, 
                                        $record->item_text, 
                                        $record->price,$record->real_price,
                                        date('Y-m-d', $record->date),
                                        $record->created_by_text,
                                        $record->user_id_text
                                    ]);
            }
        }
        $file_name = '财务'.date('Y-m-d', time());

        Excel::create($file_name,function($excel) use ($cellData){
            $excel->sheet('列表', function($sheet) use ($cellData){
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }
}



















