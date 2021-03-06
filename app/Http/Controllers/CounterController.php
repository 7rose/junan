<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Carbon\Carbon;
use Excel;

use App\Helpers\Counter;
use App\Helpers\Date;
use App\Helpers\Error;
use App\Helpers\Auth;
use App\Helpers\Logs;

class CounterController extends Controller
{
    private $auth;



    // 财务准备 
    private function financePre()
    {
        if(!Session::has('date_range')){
            $date = new Date;
            $range = $date->dateRange('month');
            if($range) Session::put('date_range', $range);
        } 

        $date_range_int = [intval(Session::get('date_range')['range'][0]), intval(Session::get('date_range')['range'][1])];

        $pre = DB::table('finance')
                    ->whereBetween('finance.date', $date_range_int)
                    ->where('finance.abandon', false)
                    ->whereNotNull('finance.customer_id')
                    // ->where('checked', true)
                    // ->where('checked_2', true)
                    ->leftJoin('config', 'finance.item', '=', 'config.id')
                    ->leftJoin('customers', 'finance.customer_id', '=', 'customers.id')
                    ->leftJoin('users as c', 'finance.created_by', '=', 'c.id')
                    ->leftJoin('users as a', 'finance.user_id', '=', 'a.id')
                    ->leftJoin('branches', 'finance.branch', '=', 'branches.id')
                    ->leftJoin('branches as r', 'a.branch', '=', 'r.id');

        return $pre;
    }

    // 财务统计模式
    public function financeMode($key)
    {
         // 授权
        // $auth = new Auth;
        // $auth_error = new Error;
        // if(!$auth->admin())  return $auth_error->forbidden();
        $error = new Error;
        if($key != 'normal' && $key !='real') return $error->paramLost();
        Session::put('counter_finance_mode', $key);
        return redirect('/counter/finance');
    }

    // 财务
    public function finance()
    {
        $auth = new Auth;
        if($auth->branchLimit()) return redirect('/counter/finance/'.$auth->me->branch);

        $pre2 = $this->financePre()
                        ->select(
                            'finance.branch', 
                            'branches.text as branch_text',
                            'r.id as real_branch', 
                            'r.text as real_branch_text',
                            DB::raw('
                                group_concat(finance.in) as finance_in, 
                                group_concat(finance.price) as finance_price, 
                                group_concat(finance.real_price) as finance_real_price, 
                                group_concat(finance.item) as finance_item 
                            '));

        if(!Session::has('counter_finance_mode') || Session::get('counter_finance_mode') != 'real'){
            $records = $pre2->groupBy('finance.branch')->get();
        }else {
            $records = $pre2->groupBy('r.id')->get();
        }

        $counter = new Counter;

        $total = $this->financePre()
                    ->select('finance.in', 'finance.real_price')
                    ->get();

        $all = $counter->total($total);

        // 下载准备
        Session::put('export', ['branch'=>'军安集团', 'all'=>$all, 'records'=>$records]);

        return view('counter.finance')
                    ->with('all', $all)
                    ->with('records', $records);
    }

    // 分支机构财务
    public function financeShow($id)
    {
        $auth = new Auth;
        if($auth->branchLimit() && $id != $auth->me->branch) return redirect('/counter/finance/'.$auth->me->branch);

        $records = [];

        if(!Session::has('counter_finance_mode') || Session::get('counter_finance_mode') != 'real'){
            $records = $this->financePre()
                        ->where('finance.branch', $id)
                        // ->whereNotNull('finance.user_id')
                        ->select(
                            'finance.user_id',
                            'a.name as user_id_text', 
                            // 'branches.text as branch_text',
                            DB::raw('
                                group_concat(finance.in) as finance_in, 
                                group_concat(finance.price) as finance_price, 
                                group_concat(finance.real_price) as finance_real_price, 
                                group_concat(finance.item) as finance_item 
                            '))
                        ->groupBy('finance.user_id')
                        ->get();
        }else{
            $records = $this->financePre()
                        ->where('r.id', $id)
                        // ->whereNotNull('finance.user_id')
                        ->select(
                            'finance.user_id',
                            'a.name as user_id_text', 
                            // 'branches.text as branch_text',
                            DB::raw('
                                group_concat(finance.in) as finance_in, 
                                group_concat(finance.price) as finance_price, 
                                group_concat(finance.real_price) as finance_real_price, 
                                group_concat(finance.item) as finance_item 
                            '))
                        ->groupBy('finance.user_id')
                        ->get();
        }

        // print_r($records);

        $counter = new Counter;
        $total = '';
        if(!Session::has('counter_finance_mode') || Session::get('counter_finance_mode') != 'real'){
            $total = $this->financePre()
                        ->whereNotNull('finance.user_id')
                        ->where('finance.branch', $id)
                        ->select('finance.in', 'finance.real_price')
                        ->get();
        }else{
            $total = $this->financePre()
                        ->whereNotNull('finance.user_id')
                        ->where('r.id', $id)
                        ->select('finance.in', 'finance.real_price')
                        ->get();
        }

        $branch = DB::table('branches')->find($id)->text;

        $all = $counter->total($total);

        // 下载准备
        Session::put('export', ['branch'=>$branch, 'all'=>$all, 'records'=>$records]);

        return view('counter.show')
                    ->with('all', $all)
                    ->with('records', $records);
    }

    // 自定义统计时间
    public function postSet(Request $request)
    {
        $end_of_day = Carbon::parse(session('date_end'))->endOfDay();

        $date = $request->input('date_start').','.$end_of_day;
        $d = new Date;
        // $date = '2018-7-1,2018-7-11';
        $range = $d->dateRange($date);
        if($range) Session::put('date_range', $range);

        return 200;
    }

    // 按月和按年统计
    public function set($string)
    {
        $d = new Date;
        $str = explode('-', $string);

        $date = $str[0];
        $path = isset($str[1]) && $str[1] == 'biz' ? '/counter/biz' : '/counter/finance';

        $range = $d->dateRange($date);

        if($range) Session::put('date_range', $range);

        // return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'统计周期已成功切换!']);

        return redirect($path);
    }

    // 下载excel
    public function getExcel($key)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $cell_1_text = $key=='all' ? '驾校' : '员工';
        // $cell_1_text = $key=='all' ? '驾校' : '员工';

        $error = new Error;
        if(!Session::has('export')) return $error->paramLost();

        $cellData = [
            [$cell_1_text, '贡献', '业务数', '实际盈收', '招生数', '招生营收', '招生营收比例', '高档班数', '高档班营收', '高档班营收比例'],
        ];

        $export = Session::get('export');
        $records = $export['records'];
        $all = $export['all'];
        $counter = new Counter;

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        $key=='all' ? $record->branch : $record->user_id_text,
                                        $counter->percent($counter->fllow($record)['all'][2], $all['total']).'%',
                                        $counter->fllow($record)['total'],
                                        '¥'.$counter->fllow($record)['all'][2],
                                        $counter->fllow($record)['recruit'][0],
                                        $counter->fllow($record)['recruit'][2],
                                        $counter->percent($counter->fllow($record)['recruit'][2], $counter->fllow($record)['all'][2]).'%',
                                        $counter->fllow($record)['change_class'][0],
                                        $counter->fllow($record)['change_class'][2],
                                        $counter->percent($counter->fllow($record)['change_class'][2], $counter->fllow($record)['all'][2]).'%'
                                    ]);
            }
        }
        $date_text = Session::get('date_range')['text'];

        $file_name = $export['branch'].'-'.$date_text.'-'.$export['all']['total_num'].'/'.$export['all']['total'];
        // 日志
        $log_content = "统计: 下载财务统计Excel";
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

    // 考务预处理
    public function lessonPre()
    {
        $pre = DB::table('lessons')
                        ->leftJoin('biz', 'lessons.biz_id', '=', 'biz.id')
                        ->leftJoin('branches', 'biz.branch', '=', 'branches.id')
                        ->where('lessons.order_date', '>', 0) 
                        ->where('biz.branch', '>', 1) 
                        ->select(
                            'lessons.id',
                            'lessons.lesson',
                            'lessons.order_date',
                            'biz.branch',
                            'branches.text as branch_text',
                            DB::raw(' 
                                group_concat(lessons.pass) as lesson_pass, 
                                group_concat(lessons.doing) as lesson_doing, 
                                group_concat(lessons.end) as lesson_end
                            '));
                        
        return $pre;
    }

    // 业务
    public function lesson()
    {
        $records = $this->lessonPre()
                        ->groupBy('lessons.order_date')
                        ->groupBy('biz.branch')
                        ->groupBy('lessons.lesson')
                        ->orderBy('lessons.order_date', 'desc')
                        ->orderBy('lessons.lesson')
                        ->paginate(50);

        $sum = $this->lessonPre()
                        ->groupBy('biz.branch')
                        ->groupBy('lessons.lesson')
                        ->orderBy('biz.branch')
                        ->orderBy('lessons.lesson')
                        ->get();

        $all = $this->lessonPre()
                        ->groupBy('lessons.lesson')
                        ->orderBy('lessons.lesson')
                        ->get();

        Session::put('counter_lesson_sum', $sum);
        Session::put('counter_lesson_all', $all);
        
        return view('counter.lesson')
                    ->with('records_sum', $sum)
                    ->with('all', $all)
                    ->with('records', $records);
        // print_r($records);
    }

    // 下载excel
    public function lessonExcel()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();

        $error = new Error;
        if(!Session::has('counter_lesson_sum')) return $error->paramLost();
        if(!Session::has('counter_lesson_all')) return $error->paramLost();

        $sum = Session::get('counter_lesson_sum');
        $all = Session::get('counter_lesson_all');

        $cellData = [
            ['机构', '科目' ,'累计人次', '累计合格人次', '合格率'],
        ];

        $counter = new Counter;

        if(count($sum)) {
            foreach ($sum as $s) {
                array_push($cellData, [
                                        $s->branch_text,
                                        $s->lesson,
                                        $counter->lessonSum($s)['all'],
                                        $counter->lessonSum($s)['pass'],
                                        $counter->lessonSum($s)['percent'].'%'
                                    ]);
            }
        }

        $cellData2 = [
            ['科目' ,'累计人次', '累计合格人次', '合格率'],
        ];

        if(count($all)) {
            foreach ($all as $a) {
                array_push($cellData2, [
                                        $a->lesson,
                                        $counter->lessonSum($a)['all'],
                                        $counter->lessonSum($a)['pass'],
                                        $counter->lessonSum($a)['percent'].'%'
                                    ]);
            }
        }

        $file_name = '考务统计'.date('Y-m-d', time());

        // 日志
        $log_content = "统计: 下载考务统计Excel";
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        Excel::create($file_name,function($excel) use ($cellData, $cellData2){
            $excel->sheet('各驾校', function($sheet) use ($cellData){
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });

            $excel->sheet('军安总体', function($sheet) use ($cellData2){
                $sheet->rows($cellData2);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

    // 业务预处理
    public function bizPre()
    {
        $this->auth = new Auth;
        
        if(!Session::has('date_range')){
            $date = new Date;
            $range = $date->dateRange('month');
            if($range) Session::put('date_range', $range);
        } 

        $date_range_int = [intval(Session::get('date_range')['range'][0]), intval(Session::get('date_range')['range'][1])];

        $pre = DB::table('biz')
                    // ->whereBetween('biz.date', $date_range_int)
                    // ->orWhereBetween('biz.finish_time', $date_range_int)
                    // ->orWhere('biz.finished', false)
                    ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                    ->leftJoin('branches', 'biz.branch', '=', 'branches.id')
                    ->leftJoin('classes', 'biz.class_id', '=', 'class_id')
                    ->leftJoin('branches as cb', 'classes.branch', '=', 'cb.id')
                    ->leftJoin('config', 'biz.licence_type', '=', 'config.id')
                    ->leftJoin('users', 'biz.user_id', '=', 'users.id')
                    ->where(function ($query) {
                                // 分支机构限制
                                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                    $query->Where('biz.branch', $this->auth->branchLimitId());
                                }
                            })
                    ->select(
                            'biz.branch',
                            'branches.text as branch_text',
                            'config.text as licence_type_text',
                            DB::raw(' 
                                group_concat(biz.date) as biz_date,  
                                group_concat(biz.finish_time) as biz_finish_time,
                                group_concat(biz.finished) as biz_finish
                            '));
        return $pre;
    }

    // 业务
    public function biz()
    {
        $records = $this->bizPre()
                        ->where('biz.branch', '>', 1) 
                        ->whereNotNull('biz.licence_type')
                        ->groupBy('biz.branch')
                        ->groupBy('biz.licence_type')
                        ->orderBy('biz.branch')
                        ->orderBy('biz.licence_type')
                        ->get();

        Session::put('counter_biz', $records);

        return view('counter.biz')
                        ->with('records', $records);
    }

    // 下载excel
    public function bizExcel()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->admin())  return $auth_error->forbidden();
        
        $error = new Error;
        if(!Session::has('counter_biz')) return $error->paramLost();

        $all = Session::get('counter_biz');

        $cellData = [
            ['机构', '证照类型' ,'在学(现在)', '新招', '毕业'],
        ];

        $counter = new Counter;

        if(count($all)) {
            foreach ($all as $a) {
                array_push($cellData, [
                                        $a->branch_text,
                                        $a->licence_type_text,
                                        $counter->bizSum($a)['doing'],
                                        $counter->bizSum($a)['new'],
                                        $counter->bizSum($a)['finished']
                                    ]);
            }
        }

        $file_name = '业务统计'.date('Y-m-d', time());

        // 日志
        $log_content = "统计: 下载业务统计Excel";
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        Excel::create($file_name,function($excel) use ($cellData){
            $excel->sheet('各驾校', function($sheet) use ($cellData){
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

}















