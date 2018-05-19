<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Excel;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\FinanceSeekForm;
use App\Forms\FinanceForm;
use App\Finance;
use App\Customer;
use App\User;

use App\Helpers\Error;
use App\Helpers\ConfigList;

class FinanceController extends Controller
{
    use FormBuilderTrait;

    private $records_prepare;

    private function prepare() 
    {
        $this->records_prepare = Finance::leftJoin('config as i', 'finance.item', '=', 'i.id')
                          ->leftJoin('customers', 'finance.customer_id', '=', 'customers.id')
                          ->leftJoin('users as c', 'finance.created_by', '=', 'c.id')
                          ->leftJoin('users as u', 'finance.user_id', '=', 'u.id')
                          ->leftJoin('branches', 'finance.branch', '=', 'branches.id')
                          ->select('finance.*', 'i.text as item_text', 'customers.name as customer_id_text', 'c.name as created_by_text', 'u.name as user_id_text', 'branches.text as branch_text', 'customers.mobile as customer_mobile')
                          ->where(function ($query) { 
                                // 起时间点
                                if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_begin') && Session::get('finance_seek_array')['date_begin'] != '') {
                                    $query->Where('finance.date', '>=', strtotime(Session::get('finance_seek_array')['date_begin']));
                                }

                                // 终时间点
                                if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_end') && Session::get('finance_seek_array')['date_end'] != '') {
                                    $query->Where('finance.date', '<=', strtotime(Session::get('finance_seek_array')['date_end']));
                                }

                                // 驾校
                                if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'branch') && Session::get('finance_seek_array')['branch'] != '') {
                                    $query->Where('finance.branch', '=', Session::get('finance_seek_array')['branch']);
                                }

                                // 关键词
                                if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'key') && Session::get('finance_seek_array')['key'] != '') {
                                    $query->Where('finance.price', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('customers.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('c.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('u.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                    $query->orWhere('i.text', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                }
                            });
                          // ->orderBy('finance.created_by', 'desc')
                          // ->orderBy('finance.date', 'desc')
    }

    // index 
    public function index()
    {
        $form = $this->form(FinanceSeekForm::class, [
            'method' => 'POST',
            'url' => route('finance.seek')
        ]);

        // 查询预处理
        $this->prepare();

        // 输出排序
        $records = $this->records_prepare
                        ->orderBy('finance.created_by', 'desc')
                        ->orderBy('finance.date', 'desc')
                        ->paginate(30);

        return view('finance.index', compact('form'))->with('records', $records);
    }

        // 查询条件
    public function seek(Request $request)
    {
        $finance_seek_array = [];
        if($request->has('key')) $finance_seek_array = array_add($finance_seek_array, 'key', $request->key);
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
    public function create()
    {
        $config_list = new ConfigList;
        $id = $config_list->idFromUrl();

        $record = Customer::find($id);
        $error = new Error;

        if(!$record) return $error->notFound();

        $form = $this->form(FinanceForm::class, [
            'method' => 'POST',
            'url' => route('finance.store')
        ]);

        $title = '收付款 - '.$record->name;
        $icon = 'credit-card';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
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
        $all['date'] = strtotime($all['date']);

        $id = Finance::create($all);
        return redirect('/customer/'.$all['customer_id']);
    }

    // 输出Execl
    public function seekToExcel()
    {
        $cellData = [
            ['收付', '驾校', '学员', '学员电话', '应收/付', '实收付', '日期', '经手人', '推荐人'],
        ];

        $this->prepare();
        $records = $this->records_prepare->orderBy('finance.date')->get();

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        $record->in ? '收' : '付', 
                                        $record->branch_text, 
                                        $record->customer_id_text, 
                                        $record->customer_mobile, 
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



















