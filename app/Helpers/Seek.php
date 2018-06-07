<?php

namespace App\Helpers;

use Session;
use Request;

/**
 * 查询
 */
class Seek
{
    // 查询是否生效
    public function seeking($array, $key)
    {
        return Session::has($array) && array_has(Session::get($array), $key) && Session::get($array)[$key] != '' ? Session::get($array)[$key] : false;
    }

    // 标签
    public function seekLabel($array, $key, $string)
    {
        $sample = $this->seeking($array, $key);
        // if(!$sample)
        return $this->seekKey($string, $sample);
    }

    public function seekKey($string, $sample)
    {
        if(!$sample) return $string;
        
        $new = $new = "<span class=\"text text-info\"><strong> ".$sample." </strong></span>";
        if($sample && str_contains($string, $sample)) {
            return str_replace($sample, $new, $string);
        }else{
            return $string;
        }
    }

    // 导航按钮点击
    public function navClick($val)
    {
        $path = Request::path();
        $use_path = explode('/', $path)[0];
        return $val == $use_path ? true : false;
    }
}