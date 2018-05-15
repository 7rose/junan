<?php

namespace App\Helpers;

use App\Config;

class ConfigList
{
    // 驾驶证类型
    public function getList($val)
    {
        $records = Config::where('type', $val)->get();
        $out = [];
        if(!count($records)) return $out;

        foreach ($records as $key) {
            $out = array_add($out, $key->id, $key->text);
        } 
        return $out;      
    }
}
