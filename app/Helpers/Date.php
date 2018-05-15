<?php

namespace App\Helpers;

use Carbon\Carbon;

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
        $rang_array = explode('-', $range);
        $low = $rang_array[0];
        $high = $rang_array[1];
        $target = $this->ageFromId($id_number);
        return $target < $low || $target > $high ? false : true;
    }

    // end
}