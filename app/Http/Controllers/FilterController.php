<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\ScoreForm;

use DB;
use Session;
use Excel;
use App\Helpers\Part;
use App\Helpers\Auth;
use App\Helpers\Error;


class FilterController extends Controller
{
    use FormBuilderTrait;

    private $biz_part;
    private $auth;

    private function prepare()
    {
        $this->auth = new Auth;

        $records = DB::table('biz')
                        ->leftJoin('lessons', 'biz.id', '=', 'lessons.biz_id')
                        ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                        ->leftJoin('branches', 'biz.branch', '=', 'branches.id')
                        ->leftJoin('classes', 'biz.class_id', '=', 'class_id')
                        ->leftJoin('branches as cb', 'classes.branch', '=', 'cb.id')
                        ->leftJoin('config', 'biz.licence_type', '=', 'config.id')
                        ->leftJoin('users', 'biz.user_id', '=', 'users.id')
                        ->where('biz.finished', false)
                        ->where(function ($query) {
                        // 分支机构限制
                        if($this->auth->branchLimit() || ($this->auth->admin() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                $query->Where('biz.branch', $this->auth->branchLimitId());
                            }
                        })

                        ->select(
                            'biz.id',
                            'customers.name as customer_name',
                            'customers.mobile as customer_mobile',
                            'customers.id_number as customer_id_number',
                            'branches.text as branch_text',
                            'config.text as licence_type_text',
                            'cb.text as class_branch_text',
                            'classes.class_no as class_no',
                            'users.name as user_name',
                            DB::raw('count(lessons.id) as lessons_num, max(lessons.lesson) as max_lesson')
                        );
        return $records;
    }

    // 处理器
    private function router($key)
    {
        switch ($key) {
            case 'no_class':
                $tmp = $this->prepare()
                        ->where('biz.finished', false)
                        ->whereNull('biz.class_id');
                return $tmp;
                break;

            case 'ready_for_1': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->where('biz.next', '1.0'); 
                                            
                return $next;
                break;

            case 'date_for_1': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->where('biz.next', '1.1'); 
                                            
                return $next;
                break;

            case 'fail_for_1': 
                /**
                 * 1. 已开班
                 * 2. 没有科目成绩记录的
                 * 3. 有记录, 但只有科目1记录, 且没过的
                 */
                $fail = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->where('lessons.lesson', 1)
                            ->where('lessons.end', false)   
                            ->where('lessons.doing', false);

                return $fail;
                break;

            case 'ready_for_2':  
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->whereNotNull('biz.user_id')
                            ->where('biz.next', '1.3')
                            ->where('biz.next2', '2.0'); 
                                            
                return $next;
                break;

            case 'date_for_2': 
                 $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->whereNotNull('biz.user_id')
                            ->where('biz.next', '1.3')
                            ->where('biz.next2', '2.1'); 
                                            
                return $next;
                break;

            case 'fail_for_2': 
                $fail = $this->prepare()
                            ->where('biz.next', '1.3')
                            ->where('biz.next2', '2.0')
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->whereNotNull('biz.user_id')
                            ->where('lessons.lesson', 2)
                            ->where('lessons.end', false)   
                            ->where('lessons.doing', false);
                return $fail;
                break;

            case 'ready_for_3':  
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->whereNotNull('biz.user_id')
                            ->where('biz.next', '1.3')
                            ->where('biz.next3', '3.0'); 
                                            
                return $next;
                break;

            case 'date_for_3': 
                 $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->whereNotNull('biz.user_id')
                            ->where('biz.next', '1.3')
                            ->where('biz.next3', '3.1'); 
                                            
                return $next;
                break;

            case 'fail_for_3': 
                /**
                 * 1. 已开班
                 * 2. 没有科目成绩记录的
                 * 3. 有记录, 但只有科目1记录, 且没过的
                 */
                $fail = $this->prepare()
                            ->where('biz.next', '1.3')
                            ->where('biz.next3', '3.0')
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->whereNotNull('biz.user_id')
                            ->where('lessons.lesson', 3)
                            ->where('lessons.end', false)   
                            ->where('lessons.doing', false);

                return $fail;
                break;

            case 'ready_for_4': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->where('biz.next', '4.0'); 
                                            
                return $next;
                break;

            case 'date_for_4': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->where('biz.next', '4.1'); 
                                            
                return $next;
                break;

            case 'fail_for_4': 
                /**
                 * 1. 已开班
                 * 2. 没有科目成绩记录的
                 * 3. 有记录, 但只有科目1记录, 且没过的
                 */
                $fail = $this->prepare()
                            ->where('biz.next', '4.0')
                            ->whereNotNull('biz.class_id')
                            ->where('biz.finished', false)
                            ->whereNotNull('biz.branch')
                            ->where('lessons.lesson', 4)
                            ->where('lessons.end', false)   
                            ->where('lessons.doing', false);

                return $fail;
                break;

            default:
                return $this->prepare();
                break;
        }
    }
    // 默认跳转
    public function index()
    {
        return redirect('/filter/no_class');
    }

    // 过滤器
    public function filter($key)
    {
        $records = $this->router($key)
                        ->groupBy('biz.id')
                        ->orderBy('biz.branch')
                        ->orderBy('biz.user_id')
                        ->get();
                        // ->toArray();

        for ($i=0; $i < count($records); $i++) { 
            if(!$records[$i]->customer_id_number) unset($records[$i]);
        }

        // 取记录集
        // $this->biz_part = $records;
        // print_r($records);
        Session::put('biz_records', $records);

        return view('filters.index')->with('records', $records);
    }


    // part
    public function ex(Request $request)
    {
        $key = $request->action;

        switch ($key) {
            case 'no_class':
                return redirect('/import/class') ;
                break;

            case 'ready_for_1': 
                $post_url = '/filter/ready/ex';
                $btn_txt = '同批提交至: 科目1预约';

                // return $this->exNote($request, $post_url, $btn_txt);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=false, $lesson=1, $order_date=false);
                break;

            case 'date_for_1': 
                $post_url = '/filter/date/ex';
                $btn_txt = '批量设置科目1考试日期';

                // return $this->exNote($request, $post_url, $btn_txt, $date_input=true);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=true, $lesson=1, $order_date=false);
                break;

            case 'score_ex': 
                $post_url = '/filter/score_ex/save';
                $btn_txt = '登记为通过';

                return $this->exNote($request, $post_url, $btn_txt, $date_input=false, $lesson=$request->lesson, $order_date=$request->order_date);
                break;

            case 'ready_for_2': 
                $post_url = '/filter/ready/ex';
                $btn_txt = '同批提交至: 科目2预约';

                // return $this->exNote($request, $post_url, $btn_txt);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=false, $lesson=2, $order_date=false);
                break;

            case 'date_for_2': 
                $post_url = '/filter/date/ex';
                $btn_txt = '批量设置科目2考试日期';

                // return $this->exNote($request, $post_url, $btn_txt, $date_input=true);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=true, $lesson=2, $order_date=false);
                break;

            case 'ready_for_3': 
                $post_url = '/filter/ready/ex';
                $btn_txt = '同批提交至: 科目3预约';

                // return $this->exNote($request, $post_url, $btn_txt);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=false, $lesson=3, $order_date=false);
                break;

            case 'date_for_3': 
                $post_url = '/filter/date/ex';
                $btn_txt = '批量设置科目3考试日期';

                // return $this->exNote($request, $post_url, $btn_txt, $date_input=true);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=true, $lesson=3, $order_date=false);
                break;

            case 'ready_for_4': 
                $post_url = '/filter/ready/ex';
                $btn_txt = '同批提交至: 科目4预约';

                // return $this->exNote($request, $post_url, $btn_txt);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=false, $lesson=4, $order_date=false);
                break;

            case 'date_for_4': 
                $post_url = '/filter/date/ex';
                $btn_txt = '批量设置科目4考试日期';

                // return $this->exNote($request, $post_url, $btn_txt, $date_input=true);
                return $this->exNote($request, $post_url, $btn_txt, $date_input=true, $lesson=4, $order_date=false);
                break;
            
            default:
                return $this->prepare();
                break;
        }
    }

    // 处理器: 准备
    public function readyEx (Request $request)
    {
        $resault = $request->type == 1 ? $request->diff_id :$request->spec_id;

        $error = new Error;
        if(!$resault) return $error->paramLost();

        $array_resault = explode(',', $resault);
        $out = [];

        foreach ($array_resault as $key) {
            $item = ['biz_id'=>$key, 'lesson'=>$request->lesson];
            array_push($out, $item);
        }

        // DB::table('lessons')
        //         ->whereIn('biz_id', $array_resault)
        //         ->where('lesson', $request->lesson)
        //         ->update(['doing'=>true]);

        DB::table('lessons')->insert($out);
        // DB::table('biz')->whereIn('id', $array_resault)->update(['next'=>$request->lesson.'.1']);
        // 未过标记
        if($request->lesson==2 || $request->lesson==3) {
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'.$request->lesson => $request->lesson.'.1']);
        }else{
            DB::table('biz')->whereIn('id', $array_resault)->update(['next' => $request->lesson.'.1']);
        }

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'批处理已成功!']);
    }

    // 处理器: 预约日期
    public function dateEx (Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->info())  return $auth_error->forbidden();

        $resault = $request->type == 1 ? $request->diff_id :$request->spec_id;

        $error = new Error;
        if(!$resault) return $error->paramLost();

        $array_resault = explode(',', $resault);

        DB::table('lessons')
            // ->havingRaw('max(lesson) = 1')
            ->where('lesson', $request->lesson)
            ->whereIn('biz_id', $array_resault)
            ->where('ready', true)
            ->where('order_date',0)
            ->where('pass', false)
            ->where('end', false)
            ->where('doing', true)
            ->update(['order_date'=> strtotime($request->date)]);

            // print_r($request->all());
        // DB::table('biz')->whereIn('id', $array_resault)->update(['next'=>$request->lesson.'.2']);
            // 未过标记
        if($request->lesson==2 || $request->lesson==3) {
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'.$request->lesson => $request->lesson.'.2']);
        }else{
            DB::table('biz')->whereIn('id', $array_resault)->update(['next' => $request->lesson.'.2']);
        }

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'批处理已成功!']);
    }

    // 科目1准备好预约
    private function exNote($request, $post_url, $btn_txt, $date_input=false, $lesson=false, $order_date=false)
    {
        $all_id = $request->all_id;
        $post_data = $request->post_data;

        $error = new Error;
        if(!$all_id) return $error->paramLost();

        $all_id = substr($all_id,0,strlen($all_id)-1); 
        $all = explode(',', $all_id);
        $all_num = count($all);
        $special_num = 0;

        if($post_data) {
            $arr  = explode(',', $post_data);
            $special_num = count($arr);
            $all = array_diff($all, $arr);
        }

        $real_num = count($all);
        $diff_id = implode(',', $all);

        $txt = "<h3>多条数据将同时处理!</h3>符合条件的记录共有".$all_num."条, 其中标记".$special_num."条";

        return view('part')
                        ->with('lesson', $lesson)
                        ->with('order_date', $order_date)
                        ->with('txt', $txt)
                        ->with('date_input', $date_input)
                        ->with('post_url', $post_url)
                        ->with('btn_txt', $btn_txt)
                        ->with('all_id', $all_id)
                        ->with('spec_id', $post_data)
                        ->with('diff_id', $diff_id);

    }

    // 成绩导入
    public function score ()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->info())  return $auth_error->forbidden();

        $form = $this->form(ScoreForm::class, [
            'method' => 'POST',
            'url' => route('score.ex')
        ]);

        $title = '成绩处理';
        $icon = 'check';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 学员列表
    public function score_ex (Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->info())  return $auth_error->forbidden();

        $order_date = strtotime($request->order_date);

        $records = $this->prepare()
                        ->whereNotNull('biz.class_id')
                        // ->havingRaw('max(lessons.lesson) = '.$request->lesson)
                        ->where('lessons.lesson', $request->lesson)
                        ->where('lessons.ready', true)
                        ->where('lessons.order_date', $order_date)
                        ->where('lessons.pass', false)
                        ->groupBy('biz.id')
                        ->get();

        return view('filters.index')
                    ->with('order_date', $order_date)
                    ->with('lesson', $request->lesson)
                    ->with('records', $records);
    }

    // 保存成绩
    public function score_save(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->info())  return $auth_error->forbidden();

       $resault = $request->type == 1 ? $request->diff_id :$request->spec_id;
       $others = $request->type == 2 ? $request->diff_id :$request->spec_id;

        $error = new Error;
        if(!$resault) return $error->paramLost();

        // 失败者
        if($others) {
            $others_array = explode(',', $others);

            DB::table('lessons')
                ->where('lesson', $request->lesson)
                ->whereIn('biz_id', $others_array)
                ->where('ready', true)
                ->where('order_date', $request->order_date)
                ->where('pass', false)
                ->where('doing', true)
                ->update(['doing'=>false]);

            // 未过标记
            if($request->lesson==2 || $request->lesson==3) {
                DB::table('biz')->whereIn('id', $others_array)->update(['next'.$request->lesson => $request->lesson.'.0']);
            }else{
                DB::table('biz')->whereIn('id', $others_array)->update(['next' => $request->lesson.'.0']);
            }

        } 

        $array_resault = explode(',', $resault);
        // print_r($request->all());
        // 成功者
        DB::table('lessons')
            // ->havingRaw('max(lesson) = '.$request->lesson)
            ->where('lesson', $request->lesson)
            ->whereIn('biz_id', $array_resault)
            ->where('ready', true)
            ->where('order_date', $request->order_date)
            ->where('pass', false)
            ->where('doing', true)
            ->update(['pass'=> true, 'doing'=>false]);

        DB::table('lessons')
            ->where('lesson', $request->lesson)
            ->whereIn('biz_id', $array_resault)
            ->update(['end'=>true]);

        if($request->lesson==1) {
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'=>$request->lesson.'.3']);
        }elseif ($request->lesson==2 || $request->lesson==3) {
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'.$request->lesson=>$request->lesson.'.3']);
            
            DB::table('biz')
                    ->where('next', '1.3')
                    ->where('next2', '2.3')
                    ->where('next3', '3.3')
                    ->update(['next'=>'4.0']);

        }else{
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'=>(intval($request->lesson)+1).'.0']);
        }

        DB::table('biz')->where('next', '5.0')->update(['finished'=>true]);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'批处理已成功!']);
    }

    // Excel
    public function filterToExcel()
    {
        $error = new Error;
        $auth = new Auth;
        if(!$auth->user()) return $error->forbidden();
        if(!Session::has('biz_records')) return $error->paramLost();

        $cellData = [
            ['姓名', '电话', '身份证', '驾校', '教练', '开班信息', '证型']
        ];
        $records = Session::get('biz_records');

        if(count($records)){
            foreach ($records as $record) {
                array_push($cellData, [
                                        $record->customer_name, 
                                        $record->customer_mobile, 
                                        '#'.$record->customer_id_number, 
                                        $record->branch_text, 
                                        $record->user_name,
                                        explode('(', $record->class_branch_text)[0].$record->class_no, 
                                        $record->licence_type_text 
                                    ]);
            }
        }

        $file_name = '考务'.date('Y-m-d', time());

        Excel::create($file_name,function($excel) use ($cellData){
            $excel->sheet('列表', function($sheet) use ($cellData){
                $sheet->rows($cellData);
                $sheet->setAutoSize(true);
                $sheet->freezeFirstRow();
            });
        })->export('xlsx');
    }

}









