<?php
namespace App\Helpers;

use DB;
use Session;
use App\Helpers\Auth;
use App\Helpers\ConfigList;

/**
 * 预处理
 */
class Pre
{
    // 更新财务信息
    public function updateFinance() 
    {
        // DB::table('customers')->update($this->ex());
        $records_array = $this->getRecordsArray();

        if($records_array) {
            foreach ($records_array as $record) {
                // array_push($out, $this->getInsertItem($record));
                DB::table('customers')->where('id', $record->id)->update(['finance_info' => $this->getFinanceNum($record)]);
            }
        }
        // return $out;
    }

    // 数据库获取信息
    private function getRecordsArray($value='')
    {
        $records = DB::table('customers')
                        ->leftJoin('finance', 'customers.id', '=', 'finance.customer_id')
                        ->select(
                            'customers.id', 
                            DB::raw('
                                group_concat(finance.customer_id) as num, 
                                group_concat(finance.price) as all_price, 
                                group_concat(finance.real_price) as all_real_price, 
                                group_concat(finance.in) as all_in
                                '))
                        ->groupBy('customers.id')
                        ->get()
                        ->toArray();
        return $records;
    }

    // 单条修改模型
    private function getFinanceNum($record)
    {
        $in_array = explode(',', $record->all_in);
        $price_array = explode(',', $record->all_price);
        $real_price_array = explode(',', $record->all_real_price);

        $n = 0;
        for ($i=0; $i < count($in_array) ; $i++) { 
            $in_array[$i] == 1 ? $n += (floatval($price_array[$i]) - floatval($real_price_array[$i])) : $n += (floatval($real_price_array[$i]) - floatval($price_array[$i]));
        }
        return $n;
    }    

    // 计算财务结果
    private function ex()
    {
        $records_array = $this->getRecordsArray();
        $out =[];
        if($records_array) {
            foreach ($records_array as $record) {
                array_push($out, $this->getFinanceNum($record));
            }
        }
        return $out;
    }

    // 获取首页工作范围 
    public function navBranches()
    {
        $auth = new Auth;

        if($auth->branchLimit()) {
            $branch_text = DB::table('branches')->find($auth->branchLimit())->text;
            return '<li class="active"><a>'.$branch_text.'</a></li>';
        }else{
            $branch = new ConfigList;
            $branch_list = $branch->branchList();

            $root_branch_text = '军安集团';
            if(Session::has('branch_set')) {
                $root_branch_text = DB::table('branches')->find(Session::get('branch_set'))->text;
            }

            $menu = '';
            foreach ($branch_list as $key => $value) {
                $menu .= '<li><a href="/branch/set/'.$key.'">'.$value.'</a></li>';
            }


            return '<li class="dropdown active">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            '.$root_branch_text.' <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu pull-right">'
                        .$menu.
                        '</ul>
                    </li>';
        }
    }

    // 驾校认领
    public function customerBranch($record)
    {
        if(!$record->licence_type_text) return '<span class="label label-default">无业务</span>';
        // if(!$record->class_id) return '<span class="label label-default">未开班</span>';

        $array_branch = explode(',', $record->biz_branch);
        $array_branch_text = explode(',', $record->biz_branch_text);
        $array_licence_type_text = explode(',', $record->licence_type_text);

        $text ='';
        for ($i=0; $i < count($array_branch); $i++) { 
            if($array_branch[$i]){
                $color = $record->biz_user_id ? 'success' : 'warning';
                $text .= '<span class="label label-'.$color.'">'.explode(':', $array_licence_type_text[$i])[0].':'.explode('(', $array_branch_text[$i])[0].'</span>';
            }else{
                $auth = new Auth;
                if($auth->branchLimit()){
                    $text .= '<a href="/biz/claim/'.$record->id.'" class="btn btn-xs btn-danger">'.explode(':', $array_licence_type_text[$i])[0].' - 本校认领'.'</a>';
                }else{
                    $text .= '<span class="label label-danger">'.explode(':', $array_licence_type_text[$i])[0].' - 无驾校!'.'</a>';
                }
            }
        }
        return $text;
    }

    // 财务统计
    public function customerFinance($record)
    {
        $array_in = explode(',', $record->finance_in);
        $array_price = explode(',', $record->finance_price);
        $array_real_price = explode(',', $record->finance_real_price);

        $num = 0;
        for ($i=0; $i < count($array_in); $i++) { 
            if($array_in[$i]){
                $num += floatval($array_price[$i]) - floatval($array_real_price[$i]);
            }else{
                $num += floatval($array_real_price[$i]) - floatval($array_price[$i]);
            }
        }
        return $num;
    }

    // 用户业务信息
    public function customerBiz($record)
    {
        $auth = new Auth;

        $edit = $auth->admin() ? '<a href="/biz/edit/'.$record->id.'" class="btn btn-sm btn-default">修改</a>' : '';
        $close = $auth->admin() ? '<a href="/biz/close/'.$record->id.'" class="btn btn-sm btn-default">关闭业务!</a>' : '';
        $open = $auth->root() ? '<a href="/biz/open/'.$record->id.'" class="btn btn-sm btn-danger">重新打开业务!</a>' : '';

        $class_info = $record->class_id ? explode('(', $record->class_branch_text)[0].$record->class_no.'期' : '<span class="label label-warning">未开班</span>';
        $user_text = $record->user_id ? $record->user_id_text : '无教练';

        if($record->finished){
            $txt = $record->licence_type_text.', '.$record->branch_text.', '.$record->class_type_text.', '.$class_info.', '.$user_text.$open;
        }else{
            $txt = $record->licence_type_text.', '.$record->branch_text.', '.$record->class_type_text.', '.$class_info.$this->userList($record->branch, $user_text, $record->id).', '.$user_text.' '.$edit.' '.$close;
        }

        return $txt;
    }

    // 教练列表
    private function userList($branch, $text, $id)
    {
        if(!$branch) return '';

        $teacher_id = 49;
        $teachers = DB::table('users')->where('branch', $branch)->where('user_type', $teacher_id)->get();

        $list = '';

        foreach ($teachers as $teacher) {
            $list .= '<li role="presentation"><a role="menuitem" tabindex="-1" href="/biz/teacher/'.$id.'-'.$teacher->id.'">'.$teacher->name.'</a></li>';
        }

        $menu = '<div class="dropdown pull-right">
                    <button type="button" class="btn btn-xs btn-default dropdown-toggle" id="dropdownMenu1" data-toggle="dropdown">'.$text.'
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                        '.$list.'
                    </ul>
                </div>';
        return $menu;
    }

    // 科目列表
    public function lessonList($record)
    {
        if(!$record->lesson) return false;

        $lessons = explode(',', $record->lesson);
        if(!count($lessons)) return "没有科目相关记录";

        $ready = explode(',', $record->lesson_ready);
        $order_date = explode(',', $record->lesson_order_date);
        $pass = explode(',', $record->lesson_pass);
        $doing = explode(',', $record->lesson_doing);
        $end = explode(',', $record->lesson_end);

        $line = '';

        for ($i=0; $i < count($lessons); $i++) { 
            $color = 'default';

            $ready_txt = '-';
            $pass_txt = '-';
            $date_txt = '-';

            if(intval($ready[$i])==1) $ready_txt='已申请';
            if(intval($order_date[$i])!=0) $date_txt = date('Y-m-d', $order_date[$i]);
            if(intval($pass[$i])==1) $pass_txt='<span class="label label-success">通过</span>';
            if(intval($pass[$i])==0 && $order_date[$i] != '' && intval($doing[$i])==0) $pass_txt='<span class="label label-danger">不通过</span>';

            $line .= '<tr class="'.$color.'"><td>'.$lessons[$i].'</td><td>'.$ready_txt.'</td><td>'.$date_txt.'</td><td>'.$pass_txt.'</td></tr>';
        }


        return $line;
    }

    // end
}






