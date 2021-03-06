<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\ScoreForm;
use Carbon\Carbon;

use DB;
use URL;
use Session;
use Excel;
use PDF;
use App\Helpers\Part;
use App\Helpers\Auth;
use App\Helpers\Error;
use App\Helpers\Logs;


class FilterController extends Controller
{
    use FormBuilderTrait;

    // private $biz_part;
    private $auth;

    private function prepare()
    {
        $this->auth = new Auth;

        $records = DB::table('biz')
                        ->leftJoin('lessons', 'biz.id', '=', 'lessons.biz_id')
                        ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                        ->leftJoin('branches', 'biz.branch', '=', 'branches.id')
                        ->leftJoin('classes', 'biz.class_id', '=', 'classes.id')
                        ->leftJoin('branches as bb', 'classes.branch', '=', 'bb.id')
                        ->leftJoin('config', 'biz.licence_type', '=', 'config.id')
                        ->leftJoin('config as g', 'customers.gender', '=', 'g.id')
                        ->leftJoin('users', 'biz.user_id', '=', 'users.id')
                        ->where('biz.finished', false)
                        ->whereNotNull('customers.id_number')
                        ->select(
                            'biz.*',
                            'customers.name as customer_name',
                            'customers.mobile as customer_mobile',
                            'customers.id_number as customer_id_number',
                            'g.text as customer_gender',
                            'branches.text as branch_text',
                            'config.text as licence_type_text',
                            'bb.text as class_branch_text',
                            'classes.class_no as class_no',
                            'users.name as user_name',
                            DB::raw('count(lessons.id) as lessons_num, max(lessons.lesson) as max_lesson')
                        )
                        ->where(function ($query) {
                            if(Session::has('filter_key')){
                                $query->where('customers.name', 'LIKE', '%'.Session::get('filter_key').'%');
                                // $query->orWhere('biz.file_id', 'LIKE', '%'.Session::get('filter_key').'%');
                                $query->orWhere('customers.mobile', 'LIKE', '%'.Session::get('filter_key').'%');
                                $query->orWhere('customers.id_number', 'LIKE', '%'.Session::get('filter_key').'%');
                                $query->orWhere('branches.text', 'LIKE', '%'.Session::get('filter_key').'%');
                                $query->orWhere('users.name', 'LIKE', '%'.Session::get('filter_key').'%');
                                $query->orWhere('classes.class_no', 'LIKE', '%'.Session::get('filter_key').'%');
                            }

                            // 分支机构限制
                            if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                $query->Where('biz.branch', $this->auth->branchLimitId());
                            }
                        });

        return $records;
    }

    // 处理器
    private function router($key)
    {
        switch ($key) {
            case 'file_id_fail':
                $tmp = $this->prepare()
                        ->whereNotNull('biz.class_id')
                        // ->where('biz.finished', false)
                        ->where('biz.branch', '>', 1)
                        // ->whereNull('biz.user_id');
                        ->whereNull('biz.file_id');
                return $tmp;
                break;

            case 'user_id_fail':
                $tmp = $this->prepare()
                        ->whereNotNull('biz.class_id')
                        // ->where('biz.finished', false)
                        ->where('biz.branch', '>', 1)
                        ->whereNull('biz.user_id');
                        // ->orWhereNull('biz.file_id');
                return $tmp;
                break;

            case 'no_class':
                $tmp = $this->prepare()
                        // ->where('biz.finished', false)
                        ->whereNull('biz.class_id');
                return $tmp;
                break;

            case 'ready_for_1': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->where('biz.next', '1.0'); 
                                            
                return $next;
                break;

            case 'date_for_1': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
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
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->where('lessons.lesson', 1)
                            ->where('lessons.end', false)   
                            ->where('lessons.doing', false);

                return $fail;
                break;

            case 'ready_for_2':  
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->whereNotNull('biz.user_id')
                            ->where('biz.next', '1.3')
                            ->where('biz.next2', '2.0'); 
                                            
                return $next;
                break;

            case 'date_for_2': 
                 $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
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
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->whereNotNull('biz.user_id')
                            ->where('lessons.lesson', 2)
                            ->where('lessons.end', false)   
                            ->where('lessons.doing', false);
                return $fail;
                break;

            case 'ready_for_3':  
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->whereNotNull('biz.file_id')
                            ->whereNotNull('biz.user_id')
                            ->where('biz.next', '1.3')
                            ->where('biz.next3', '3.0'); 
                                            
                return $next;
                break;

            case 'date_for_3': 
                 $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->whereNotNull('biz.file_id')
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
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->whereNotNull('biz.user_id')
                            ->where('lessons.lesson', 3)
                            ->where('lessons.end', false)   
                            ->where('lessons.doing', false);

                return $fail;
                break;

            case 'ready_for_4': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
                            ->where('biz.next', '4.0'); 
                                            
                return $next;
                break;

            case 'date_for_4': 
                $next = $this->prepare()
                            ->whereNotNull('biz.class_id')
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
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
                            // ->where('biz.finished', false)
                            ->where('biz.branch', '>', 1)
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
        // 清除选择
        $this->clearSelect();

        return redirect('/filter/file_id_fail');
    }

    // 查询
    public function seek(Request $request)
    {
        if($request->key && trim($request->key) != '') Session::put('filter_key', trim($request->key));
        if((!$request->key || trim($request->key) == '') && Session::has('filter_key')) Session::forget('filter_key');
        return redirect('/'.$request->path);
    }

    // 查询
    public function seekReset(Request $request)
    {
        if(Session::has('filter_key')) Session::forget('filter_key');
        return redirect('/'.$request->path);
    }

    // 标记
    public function select(Request $request, $id)
    {
        if(Session::has('filter_select')) {
            $old = Session::get('filter_select');
            array_push($old, $id);
            $new = array_unique($old);

            Session::put('filter_select', $new);
        }else{
            Session::put('filter_select', [$id]);
        }

        return redirect($request->url);
    }

    // 取消标记
    public function cancel(Request $request, $id)
    {
        if(Session::has('filter_select')) {
            $old = Session::get('filter_select');
            $new = array_unique($old);

            // unset($new[array_search($id, $new)]);
            if(in_array($id, $new)) unset($new[array_search($id, $new)]);
            // print_r($new);

            if(count($new)) {
                Session::put('filter_select', $new);
            }else{
                Session::forget('filter_select');
            }
        }

        return redirect($request->url);
    }


    // 过滤器
    public function filter($key)
    {
        $records = $this->router($key)
                        ->groupBy('biz.id')
                        ->orderBy('biz.id')
                        ->orderBy('biz.branch')
                        ->orderBy('biz.file_id')
                        ->orderBy('biz.user_id')
                        ->get();
                        // ->toArray();

        Session::put('biz_records', $records);
        // return $records->toJson();

        $pages = $this->router($key)
                        ->groupBy('biz.id')
                        ->orderBy('biz.id')
                        ->orderBy('biz.branch')
                        ->orderBy('biz.user_id')
                        ->paginate(30);

        return view('filters.main')
                // ->with('records', $records)
                ->with('records', $this->picker($pages))
                ->with('all', count($records))
                ->with('selected_records', $this->selected($pages));
    }

    // 批处理分捡器
    public function picker($records)
    {
        if(count($records)){
            if(Session::has('filter_select') && count(Session::get('filter_select'))){
                $selected_array = Session::get('filter_select');
                foreach ($records as $record) {
                    $record->selected = in_array($record->id, $selected_array) ? true : false;
                }
            }else{
                foreach ($records as $record) {
                    $record->selected = false;
                }
            }
        }
        return $records;
    }

    // 分捡部分
    public function selected($records)
    {
        $selected_records=[];

        if(count($records) && Session::has('filter_select')){
            $selected_array = Session::get('filter_select');
            $selected_records = DB::table('biz')
                                ->leftJoin('customers', 'biz.customer_id', '=', 'customers.id')
                                ->whereIn('biz.id', $selected_array)
                                ->select('biz.id','customers.name as customer_name', 'customers.id_number as customer_id_number')
                                ->orderBy('biz.id')
                                ->get();

        }
        return $selected_records;
    }

    // 排除法
    public function ex1($key)
    {
        $error = new Error;
        $ex_array = [];

        if(Session::has('biz_records') && count(Session::get('biz_records'))){
            $new_biz_records = Session::get('biz_records');
            $main_array = [];
            $rest_array = [];

            if(Session::has('filter_select') && count(Session::get('filter_select'))){
                $selected_array = Session::get('filter_select');
                if(count($selected_array) == count($new_biz_records)) return $error->paramLost();
                foreach ($new_biz_records as $record) {
                    if(!in_array($record->id, $selected_array))  array_push($main_array, $record->id);
                    // $new_biz_records->main = in_array($record->id, $selected_array) ? false : true;
                }
                $rest_array = array_values($selected_array);
            }else{
                foreach ($new_biz_records as $record) {
                    array_push($main_array, $record->id);
                }
            }

            $ex_array = ['main'=>$main_array, 'rest'=>$rest_array];
            Session::put('ex_array', $ex_array);

            return $this->note2($key);
            // echo $key;
        }
    }

    // 仅标记
    public function ex2($key)
    {
        $error = new Error;
        $ex_array = [];

        if(Session::has('biz_records') && count(Session::get('biz_records'))){
            $new_biz_records = Session::get('biz_records');
            $main_array = [];
            $rest_array = [];

            if(Session::has('filter_select') && count(Session::get('filter_select'))){
                $selected_array = Session::get('filter_select');

                foreach ($new_biz_records as $record) {
                    if(!in_array($record->id, $selected_array))  array_push($rest_array, $record->id);
                }
                $main_array = array_values($selected_array);
            }else{
                return $error->paramLost();
            }
            $ex_array = ['main'=>$main_array, 'rest'=>$rest_array];
            Session::put('ex_array', $ex_array);

            return $this->note2($key);
        }
    }

    // 二次提示
    private function note2($key)
    {
        switch ($key) {
            case 'no_class':
                return redirect('/import/class') ;
                break;

            case 'ready_for_1': 
                $post_url = '/filter/do/ready';
                $btn_txt = '同批提交至: 科目1预约';
                return view('filters.note')
                                ->with('lesson', 1)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;

            case 'date_for_1': 
                $post_url = '/filter/do/date';
                $btn_txt = '批量设置科目1考试日期';

                return view('filters.note')
                                ->with('lesson', 1)
                                ->with('date_input', true)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;

            case 'score': 
                $post_url = '/filter/save/score';
                $btn_txt = '登记为通过';

                return view('filters.note')
                                ->with('lesson', Session::get('score_lesson'))
                                // ->with('date_input', true)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);

            case 'ready_for_2': 
                $post_url = '/filter/do/ready';
                $btn_txt = '同批提交至: 科目2预约';
                return view('filters.note')
                                ->with('lesson', 2)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;

            case 'date_for_2': 
                $post_url = '/filter/do/date';
                $btn_txt = '批量设置科目2考试日期';

                return view('filters.note')
                                ->with('lesson', 2)
                                ->with('date_input', true)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;

            case 'ready_for_3': 
                $post_url = '/filter/do/ready';
                $btn_txt = '同批提交至: 科目3预约';
                return view('filters.note')
                                ->with('lesson', 3)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;

            case 'date_for_3': 
                $post_url = '/filter/do/date';
                $btn_txt = '批量设置科目3考试日期';

                return view('filters.note')
                                ->with('lesson', 3)
                                ->with('date_input', true)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;

            case 'ready_for_4': 
                $post_url = '/filter/do/ready';
                $btn_txt = '同批提交至: 科目4预约';
                return view('filters.note')
                                ->with('lesson', 4)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;

            case 'date_for_4': 
                $post_url = '/filter/do/date';
                $btn_txt = '批量设置科目4考试日期';

                return view('filters.note')
                                ->with('lesson', 4)
                                ->with('date_input', true)
                                ->with('post_url', $post_url)
                                ->with('btn_txt', $btn_txt);
                break;
            
            default:
                // return view('filters.note');
                break;
        }
    }

    // 清除选择
    private function clearSelect()
    {
        if(Session::has('filter_select')) Session::forget('filter_select');
    }

    // 科目1, 2 ,3, 4预约申请
    public function doReady(Request $request)
    {
        $error = new Error;

        if(!Session::has('ex_array') || !count(Session::get('ex_array')['main'])) return $error->paramLost();

        $array_resault = Session::get('ex_array')['main'];

        $out = [];

        foreach ($array_resault as $key) {
            $item = ['biz_id'=>$key, 'lesson'=>$request->lesson];
            array_push($out, $item);
        }

        DB::table('lessons')->insert($out);

        if($request->lesson==2 || $request->lesson==3) {
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'.$request->lesson => $request->lesson.'.1']);
        }else{
            DB::table('biz')->whereIn('id', $array_resault)->update(['next' => $request->lesson.'.1']);
        }
        // 清除选择
        $this->clearSelect();

        // 日志
        $log_content = "考务: 批量提交预约申请";
        $log_level = "info";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'批处理已成功!']);
    }

    // 科目1, 2, 3 ,4预约日期设置
    public function doDate(Request $request) 
    {
        $error = new Error;

        if(!Session::has('ex_array') || !count(Session::get('ex_array')['main'])) return $error->paramLost();

        $array_resault = Session::get('ex_array')['main'];

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

        if($request->lesson==2 || $request->lesson==3) {
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'.$request->lesson => $request->lesson.'.2']);
        }else{
            DB::table('biz')->whereIn('id', $array_resault)->update(['next' => $request->lesson.'.2']);
        }
        // 清除选择 
        $this->clearSelect();
        // 日志
        $log_content = "考务: 批量设置考试日期";
        $log_level = "info";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return view('note')->with('custom', ['color'=>'success', 'icon'=>'ok', 'content'=>'批处理已成功!']);
    }

    // 成绩批量输入界面
    public function scoreChoose ()
    {
        // // 授权
        // $auth = new Auth;
        // $auth_error = new Error;
        // if(!$auth->info())  return $auth_error->forbidden();

        // 清除选择
        $this->clearSelect();

        $form = $this->form(ScoreForm::class, [
            'method' => 'POST',
            'url' => route('score.do')
        ]);

        $title = '成绩处理';
        $icon = 'check';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);

    }
        // 科目1, 2, 3, 4成绩录入
    public function exScore($key) 
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;

        $key_array = explode('-', $key);

        Session::put('score_lesson', $key_array[0]);
        Session::put('score_date', $key_array[1]);
        Session::put('branch_set', $key_array[2]);

        if(!$auth->admin() && $auth->me->branch != intval(Session::get('branch_set')))  return $auth_error->forbidden();

        // return $this->doScoreList();
        return redirect('/filter/do/score');
    }

    // 科目1, 2, 3, 4成绩录入
    public function doScore(Request $request) 
    {
        // // 授权
        // $auth = new Auth;
        // $auth_error = new Error;
        // if(!$auth->info())  return $auth_error->forbidden();

        Session::put('score_date', strtotime($request->order_date));
        Session::put('score_lesson', $request->lesson);

        return $this->doScoreList();
    }

    // 成绩列表
    public function doScoreList()
    {
        $error = new Error;

        if(!Session::has('score_lesson') || !Session::has('score_date')) return $error->paramLost();

        $pre = $this->prepare()
                        ->whereNotNull('biz.class_id')
                        // ->havingRaw('max(lessons.lesson) = '.$request->lesson)
                        ->where('lessons.lesson', Session::get('score_lesson'))
                        ->where('lessons.ready', true)
                        ->where('lessons.order_date', Session::get('score_date'))
                        ->where('lessons.pass', false)
                        ->groupBy('biz.id');

                        // ->paginate(20);

        Session::put('biz_records', $pre->get());
        // return $records->toJson();

        $all = $pre->get()->count();
        $pages = $pre->paginate(20);
        // // 清除选择
        // $this->clearSelect();

        return view('filters.main')
                ->with('all', $all)
                ->with('records', $this->picker($pages))
                ->with('selected_records', $this->selected($pages));
    }

    // 保存成绩
    public function saveScore()
    {
        // // 授权
        $auth = new Auth;
        // $auth_error = new Error;
        // if(!$auth->info())  return $auth_error->forbidden();

       if(!Session::has('ex_array') || !count(Session::get('ex_array')['main'])) return $error->paramLost();
       if(!Session::has('score_lesson') || !Session::has('score_date')) return $error->paramLost();

       if(!$auth->admin() && $auth->me->branch != intval(Session::get('branch_set')))  return $auth_error->forbidden();


        $array_resault = Session::get('ex_array')['main'];
        $others_array = Session::get('ex_array')['rest'];
        $lesson = Session::get('score_lesson');
        $order_date = Session::get('score_date');

        // 失败者
        if(count($others_array)) {
            DB::table('lessons')
                ->where('lesson', $lesson)
                ->whereIn('biz_id', $others_array)
                ->where('ready', true)
                ->where('order_date', $order_date)
                ->where('pass', false)
                ->where('doing', true)
                ->update(['doing'=>false]);

            // 未过标记
            if($lesson==2 || $lesson==3) {
                DB::table('biz')->whereIn('id', $others_array)->update(['next'.$lesson => $lesson.'.0']);
            }else{
                DB::table('biz')->whereIn('id', $others_array)->update(['next' => $lesson.'.0']);
            }

        } 

        // print_r($request->all());
        // 成功者
        DB::table('lessons')
            // ->havingRaw('max(lesson) = '.$lesson)
            ->where('lesson', $lesson)
            ->whereIn('biz_id', $array_resault)
            ->where('ready', true)
            ->where('order_date', $order_date)
            ->where('pass', false)
            ->where('doing', true)
            ->update(['pass'=> true, 'doing'=>false]);

        DB::table('lessons')
            ->where('lesson', $lesson)
            ->whereIn('biz_id', $array_resault)
            ->update(['end'=>true]);

        if($lesson==1) {
            // 写入考试有效期
            $start_date = Carbon::createFromTimestamp($order_date);
            $out_date = $start_date->copy()->addYears(3);

            // 写入进度
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'=>$lesson.'.3', 'start_date'=>$start_date, 'out_date'=>$out_date]);
        }elseif ($lesson==2 || $lesson==3) {
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'.$lesson=>$lesson.'.3']);
            
            DB::table('biz')
                    ->where('next', '1.3')
                    ->where('next2', '2.3')
                    ->where('next3', '3.3')
                    ->update(['next'=>'4.0']);

        }else{
            DB::table('biz')->whereIn('id', $array_resault)->update(['next'=>(intval($lesson)+1).'.0']);
        }

        DB::table('biz')->where('next', '5.0')->update(['finished'=>true, 'finish_time'=>time()]);

        // 清除选择
        $this->clearSelect();
        if(Session::has('score_lesson')) Session::forget('score_lesson');
        if(Session::has('score_date')) Session::forget('score_date');

        // 日志
        $log_content = "考务: 批量处理成绩";
        $log_level = "info";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

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
                                        $record->customer_id_number.' ', 
                                        $record->branch_text, 
                                        $record->user_name,
                                        explode('(', $record->class_branch_text)[0].$record->class_no, 
                                        $record->licence_type_text 
                                    ]);
            }
        }

        $file_name = '考务'.date('Y-m-d', time());

        // 日志
        $log_content = "考务: 下载学员Excel";
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

    // 打印科目3, PDF
    public function pdf()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->info())  return $auth_error->forbidden();

        $records = $this->prepare()
                    ->whereNotNull('biz.class_id')
                    ->where('biz.finished', false)
                    ->where('biz.branch', '>', 1)
                    ->whereNotNull('biz.file_id')
                    ->whereNotNull('biz.user_id')
                    ->where('biz.next', '1.3')
                    ->where('biz.next3', '3.2')
                    ->where('biz.printed', false)
                    ->groupBy('biz.id')
                    ->orderBy('biz.branch')
                    ->orderBy('biz.user_id')
                    ->orderBy('customers.gender', 'desc')
                    ->get();
        if(!count($records)) {
            return view('note')->with('custom', ['color'=>'warning', 'icon'=>'ok', 'content'=>'成绩单每个学员只能打印一次, 可能是已经打印或无符合条件的学员!']);
        }
        $num = count($records);
        $name = date('Y-m-d', time()).'_'.$num.'人';

        $print_ids = [];
        $container = '';

        if(count($records)) {
            foreach ($records as $record) {
                array_push($print_ids, $record->id);
                $container .= '
                    <div class="container">
                        <p>约考凭证打印</p>
                        <p class="title">科目三道路驾驶技能考试预约凭证</p>
                        <div class="content">
                            <table>
                              <tbody>
                                <tr>
                                  <td class="info">
                                    <p class="info_space">身份证明号码：'.$record->customer_id_number.'</p>
                                    <p class="info_space">准考证明号码：'.$record->file_id.'</p>
                                    <p class="info_space">姓名：'.$record->customer_name.'</p>
                                    <p class="info_space">性别：'.$record->customer_gender.'</p>
                                    <p class="info_space">准考车型：'.explode(':', $record->licence_type_text)[0].'</p>
                                    <p class="info_space">考场名称：车管所考场</p>
                                  </td>
                                  <td>
                                    <table border="1" cellspacing="0" class="img_table">
                                    <tbody>
                                      <tr>
                                        <td><p class="img">照片</p></td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                            <p>科目三道路驾驶技能考试成绩单</p>
                            <table border="1" cellspacing="0" class="scroe_table">
                                <tbody>
                                    <tr class="score">
                                        <td colspan="4"><p>科目三道路驾驶技能考试</p></td>
                                    </tr>
                                    <tr>
                                        <td><p>考试日期</p></td>
                                        <td></td>
                                        <td>考试成绩</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><p>扣分项</p></td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td><p>考试员签名</p></td>
                                        <td></td>
                                        <td><p>考生签名</p></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><p>科目三道路驾驶技能补考</p></td>
                                    </tr>
                                    <tr>
                                        <td><p>考试日期</p></td>
                                        <td></td>
                                        <td>考试成绩</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><p>扣分项</p></td>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td><p>考试员签名</p></td>
                                        <td></td>
                                        <td><p>考生签名</p></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="note">
                            <p>注意事项：</p>
                            <p>1、凭此证并带好有效身份证按预约时间参加考试。</p>
                            <p>2、考试期间听从指挥，遵守考场纪律，文明待考。</p>
                            <p>3、申请人在考试过程中有贿赂、舞弊行为的，取消考试资格，已经通过考试的其他科目成<br/>绩无效。申请人在一年内不得申领机动车驾驶证。</p>
                            <p>4、办理增加准驾车型业务期间，不可到其他车管所办理转入业务。</p>
                            </div>
                        </div>
                    </div>
                    <div class="page-break"></div>';
            }
        }


        $templet = '<!doctype html>
                    <html>
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                    <title>科目3成绩单</title>
                    <style>
                        @font-face {
                            font-family: "simsun";
                            font-style: normal;
                            font-weight: normal;
                            src: url('. URL::asset("junan/fonts/simsun.ttf").') format("truetype");
                        }

                        html, body {  
                            height: 100%;  
                        }

                        p {
                            padding-top: -5px;
                            padding-bottom: -5px;
                        }

                        body {  
                            margin: 0;  
                            padding: 0;  
                            width: 100%;
                            font-size: 14px;
                            font-weight: 100;  
                            font-family: "simsun";  
                        }

                        .container {  
                            /*display: table-cell; */
                            vertical-align: middle;  
                        }
                        .content {  
                            text-align: center;  
                            display: inline-block; 
                            padding-left: 40px; 
                            padding-right: 40px; 
                        }
                        .title {  
                            font-size: 22px;  
                            text-align: center;
                            padding-top: 30px;
                            padding-bottom: 10px;
                        }
                        .info {
                            width: 480px;
                        }
                        .info_space {
                            padding-top: 5px;
                        }
                        .img_table {
                            padding-top: 40px;
                        }
                        .img {
                            padding-left: 40px;
                            padding-right:40px;
                            padding-top:40px;
                            padding-bottom: 65px;
                        }
                        .scroe_table {
                            width: 100%;
                            text-align: center;
                        }
                        .note {
                            width: 100%;
                            text-align: left;
                        }
                        .page-break {
                            page-break-after: always;
                        }

                    </style>
                    </head>
                    <body>'
                    .$container.
                    '</body>
                    </html>';
    // 标记已打印
    DB::table('biz')
        ->whereIn('id', $print_ids)
        ->update(['printed'=>true]);

    // 日志
    $log_content = "考务: 下载科目3打印文件";
    $log_level = "danger";
    $log_put = new Logs;
    $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

    // 输出PDF
    $pdf = PDF::loadHTML($templet);
    return $pdf->download($name.'.pdf');

    }

    // end

}









