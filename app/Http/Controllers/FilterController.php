<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use Session;
use App\Helpers\Part;
use App\Helpers\Auth;


class FilterController extends Controller
{
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
                            DB::raw('count(lessons.id) as lessons_num')
                        );
        return $records;
    }

    // 处理器
    private function router($key)
    {
        switch ($key) {
            case 'no_class':
                $tmp = $this->prepare()
                        ->where('biz.class_id', null);
                return $tmp;
                break;

            case 'ready_for_1': 
                // 未报名或者只报科目1并失败的
                $tmp = $this->prepare()
                        ->where('biz.class_id', '<>', null)
                        ->havingRaw('count(lessons.id) = 0');
                        // ->orHavingRaw('max(lessons.id) = 1');
                        // ->orWhere(function, ($query){
                        //     $query->havingRaw()
                        // });

                return $tmp;
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
        $records = $this->router($key)->groupBy('biz.id')->get();

        // 取记录集
        $this->biz_part = $records;

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
                $post_url = '/filter/ready_for_1/ex';
                $btn_txt = '同批提交至: 科目1预约';

                return $this->exNote($request->all_id, $request->post_data, $post_url, $btn_txt);
                break;
            
            default:
                return $this->prepare();
                break;
        }
    }

    // 科目1准备好预约
    private function exNote($all_id, $post_data, $post_url, $btn_txt)
    {
        $all_id = substr($all_id,0,strlen($all_id)-1); 
        $all = explode(',', $all_id);
        $all_num = count($all);
        $special_num = 0;

        if($post_data) {
            $arr  = explode(',', $post_data);
            $special_num = count($arr);
            $all = array_diff($all, $arr);
        }else{
            echo "fuck";
        }
        $real_num = count($all);
        $diff_id = implode(',', $all);

        $txt = "<h3>多条数据将同时处理!</h3>符合条件的记录共有".$all_num."条, 其中标记".$special_num."条";

        return view('part')
                        ->with('txt', $txt)
                        ->with('post_url', $post_url)
                        ->with('btn_txt', $btn_txt)
                        ->with('all_id', $all_id)
                        ->with('spec_id', $post_data)
                        ->with('diff_id', $diff_id);

    }

    // 标记处理
    public function ready_for_1_ex (Request $request)
    {
        $all = $request->all();
        print_r($all);
        // echo $all;
        // intval($request->type) == 1  

    }




    // public function checking(Request $request)
    // {
    //     $ticket_no = $request->input('no');
    //     $id = $request->input('id');
    //     // 授权
    //     $auth = new Auth;
    //     if(!$auth->finance())  return "无权操作";

    //     $target = Finance::find($id)->update(['checked' => true, 'checked_by'=>Session::get('id'), 'checked_by_time'=>time(), 'ticket_no'=>$ticket_no]);
    //     // return redirect('/finance');
    //     // echo 'fuck';
    //     return '票号:'.$ticket_no.'已成功审核!';
    // }

    // end
}









