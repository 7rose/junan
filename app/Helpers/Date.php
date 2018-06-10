<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Config;

class Date
{  
    // 生日
    public function birthdayFromId($id_number)
    {
        $year = mb_substr($id_number, 6, 4);
        $month = mb_substr($id_number, 10, 2);
        $day = mb_substr($id_number, 12, 2);
        return $year.'-'.$month.'-'.$day;
    }

    // 周岁
    public function ageFromId($id_number)
    {
        $birthday = new Carbon($this->birthdayFromId($id_number));
        return $birthday->age;
    }

    // 年龄限制
    public function availableId($id_number, $range)
    {
        if($range == '' || $range == null) return true;
        
        $rang_array = explode('-', $range);
        $low = $rang_array[0];
        $high = $rang_array[1];
        $target = $this->ageFromId($id_number);
        return $target < $low || $target > $high ? false : true;
    }

    // 不可办理业务
    public function badBiz($id_number)
    {
        $records = Config::where('type','licence_type')->where('extra', '<>', NULL)->where('extra', '<>', '')->get();

        $out = "";
        foreach ($records as $record) {
            if(!$this->availableId($id_number, $record->extra)){
                $out .= '<li>'.$record->text.';  要求: '.$record->extra.'周岁</li>';
            }

        }

        return $out == "" ? false : $out;
    }

    public function dateRange($date)
    {
        $now = Carbon::now();

        // 上个月
        $pre_month_start = $now->copy()->subMonth()->startOfMonth(); 
        $pre_month_end = $now->copy()->subMonth()->endOfMonth(); 
        $pre_month_year = $now->copy()->subMonth()->year; 
        $pre_month = $now->copy()->subMonth()->month; 
        
        // 本日
        $today_start = $now->copy()->startOfDay();
        $today_end = $now->copy()->endOfDay();
        $today = $now->copy()->day;

        // 本周
        $week_start = $now->copy()->startOfWeek();
        $week_end = $now->copy()->endOfWeek();

        // 本月
        $month_start = $now->copy()->startOfMonth(); 
        $month_end = $now->copy()->endOfMonth(); 
        $this_month = $now->copy()->month; 

        // 本年
        $year_start = $now->copy()->startOfYear(); 
        $year_end = $now->copy()->endOfYear();
        $this_year = $now->copy()->year;  

        if($date == 'pre_month') return ['text'=>'上个月'.$pre_month_year.'年'.$pre_month.'月份', 'range' =>[strtotime($pre_month_start), strtotime($pre_month_end)]];
        if($date == 'today') return ['text'=>'今天'.$today.'日', 'range' =>[strtotime($today_start), strtotime($today_end)]];
        if($date == 'week') return ['text'=>'本周', 'range' =>[strtotime($week_start), strtotime($week_end)]];
        if($date == 'month') return ['text'=>'本月'.$this_month.'月份', 'range' =>[strtotime($month_start), strtotime($month_end)]];
        if($date == 'year') return ['text'=>$this_year.'全年', 'range' =>[strtotime($year_start), strtotime($year_end)]];
    
        // return false;
        echo $pre_month_year;
    }

    // end
}


















