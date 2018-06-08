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
        // 当月
        $month_start = $now->startOfMonth(); 
        $month_end = $now->copy()->endOfMonth(); 
        $this_month = $now->copy()->month; 
        // 当年
        $year_start = $now->copy()->startOfYear(); 
        $year_end = $now->copy()->endOfYear();
        $this_year = $now->copy()->year;  

        if($date == 'month') return ['text'=>'今年'.$this_month.'月份', 'range' =>[strtotime($month_start), strtotime($month_end)]];
        if($date == 'year') return ['text'=>$this_year.'全年', 'range' =>[strtotime($year_start), strtotime($year_end)]];
    
        return false;
    }

    // end
}


















