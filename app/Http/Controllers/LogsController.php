<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Excel;

use App\Helpers\Auth;
use App\Helpers\Logs;
use App\Helpers\Error;

class LogsController extends Controller
{
    private $auth;

    private function pre()
    {
        $this->auth = new Auth;

        $pre = DB::table('logs')
                        ->leftJoin('users', 'logs.who', '=', 'users.id')
                        ->select(
                            'logs.*',
                            'users.name as user_name',
                            'users.id as user_id'
                        )
                        ->where(function ($query) {
                            if(Session::has('logs_date_start')){
                                $query->where('logs.created_at', '>=', Session::get('logs_date_start'));
                            }

                            if(Session::has('logs_date_end')){
                                $query->where('logs.created_at', '<=', Session::get('logs_date_end'));
                            }

                            if(Session::has('logs_key')){
                                $query->where('users.name', 'LIKE', '%'.Session::get('logs_key').'%');
                                // $query->orWhere('logs.from', 'LIKE', '%'.Session::get('logs_key').'%');
                                $query->orWhere('logs.content', 'LIKE', '%'.Session::get('logs_key').'%');
                            }

                            if(Session::has('logs_level')){
                                $query->Where('logs.level', Session::get('logs_level'));
                            }

                            // 分支机构限制
                            if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                $query->Where('users.branch', $this->auth->branchLimitId());
                            }
                        })
                        ->orderBy('logs.created_at', 'desc');
        return $pre;
    }

    // 列表
    public function index()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();
        
        $records = $this->pre()->paginate(100);
        return view('logs.index')
                    ->with('records', $records);
    
    }

    // 查询
    public function seek(Request $request)
    {
        $all = $request->all();

        if($request->date_start != '') {
            Session::put('logs_date_start', $request->date_start);
        }else{
            if(Session::has('logs_date_start')) Session::forget('logs_date_start');
        }

        if($request->date_end != '') {
            Session::put('logs_date_end', $request->date_end);
        }else{
            if(Session::has('logs_date_end')) Session::forget('logs_date_end');
        }

        if($request->key != '') {
            Session::put('logs_key', $request->key);
        }else{
            if(Session::has('logs_key')) Session::forget('logs_key');
        }

        if($request->level == 'info' || $request->level == 'warning' || $request->level == 'danger') {
            Session::put('logs_level', $request->level);
        }else{
            if(Session::has('logs_level')) Session::forget('logs_level');
        }

        return $this->index();
    }

    // Excel
    public function logsExcel()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();
        
        $cellData = [
            ['级别', '用户', '时间', '地点', '内容'],
        ];

        $records = $this->pre()->get();

        function getLevel($level)
        {
            switch ($level) {
                case 'info':
                    return '常规';
                    break;

                case 'warning':
                    return '重要';
                    break;

                case 'danger':
                    return '紧急';
                    break;

                default:
                    return '常规';
                    break;
            }
        }
        

        if(count($records)) {
            foreach ($records as $record) {
                array_push($cellData, [
                                        getLevel($record->level),
                                        $record->user_name, 
                                        $record->created_at, 
                                        $record->from, 
                                        $record->content
                                    ]);
            }
        }
        $file_name = '日志'.date('Y-m-d', time());

        // 日志
        $log_content = "日志: 下载Excel文件(可能为查询结果)";
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

    // end
}























