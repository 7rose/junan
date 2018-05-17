<?php

namespace App\Helpers;

use App\Config;
use App\Branch;
use App\Customer;
use Request;

use App\Helpers\Date;

class ConfigList
{
    // 配置类型
    public function getList($val)
    {
        $records = Config::where('type', $val)->get();
        return $this->out($records);      
    }

    // 分支机构
    public function branchList()
    {
        $records = Branch::all();
        return $this->out($records);
    }

    // 输出
    private function out($records)
    {
        $out = [];
        if(!count($records)) return $out;

        foreach ($records as $key) {
            $out = array_add($out, $key->id, $key->text);
        } 
        return $out; 
    }

    // 获取有效业务列表
    public function getBizList($id)
    {
        // $path = Request::path();
        // $path_array = explode('/', $path);
        // $id = end($path_array);

        $date = new Date;
        $id_number = Customer::find($id)->id_number;
        $licence_types = Config::where('type','licence_type')->get();

        $out = [];
        foreach ($licence_types as $key) {
            if($date->availableId($id_number, $key->extra)) $out = array_add($out, $key->id, $key->text);
        }
        return $out;
    }

    // 从地址栏获取id
    public function idFromUrl()
    {
        $path = Request::path();
        $path_array = explode('/', $path);
        return end($path_array);
    }

    // end
}
