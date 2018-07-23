<?php

namespace App\Helpers;
use Session;

class Show
{
    
    // 关键词加亮显示
    public function seekString($key, $text)
    {
       if(!Session::has($key)) return $text;
       $session_key = session($key);
        $new = "<span class=\"text text-danger\"><strong> ".$session_key." </strong></span>";
        if(str_contains($text, $session_key)) {
            return str_replace($session_key, $new, $text);
        }else{
            return $text;
        }
    }

    // 财务记录背景
    public function financeColor($record)
    {
        if($record->abandon) return "danger";
        if(!$record->in) return "warning";
    }


}