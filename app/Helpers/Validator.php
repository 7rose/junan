<?php
namespace App\Helpers;

use App\User;

 class Validator
 {
    // 18位身份证
    public function checkIdNumber($val)
    {
        $rule='/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|[xX])$/';
        return preg_match($rule,$val) ? true : false;
    }

    // 手机号
    public function checkMobile($val)
    {
        $rule = '/^1[345789]{1}\d{9}$/';
        return preg_match($rule,$val) ? true : false;
    }

    // 工号
    public function getWorkId()
    {
        $start = 1000;
        $max = User::max('work_id');
        $max = $max && $max > $start ? $max : $start;
        $work_id = $max + 1;

        $work_id_str = strval($work_id);
        if(str_contains($work_id_str, '4')) {
            $work_id_str = str_replace('4', '5', $work_id_str);
        }
        $work_id = intval($work_id_str);
        return $work_id;
    }

 }