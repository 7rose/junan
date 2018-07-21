<?php

namespace App\Helpers;

use App\Config;
use App\Branch;
use App\Customer;
use App\User;

use Request;
use Session;

use App\Helpers\Date;

class ConfigList
{
    // 配置类型
    public function getList($val)
    {
        $records = Config::where('type', $val)
                    ->where('show', true)
                    ->orderBy('text')
                    ->get();
        return $this->out($records);      
    }

    // 分支机构
    public function branchList()
    {
        $records = Branch::where('show', true)->get();
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

    // 有效授权
    public function authList()
    {
        $records = Config::where('type', 'auth_type')->get();
        $me = User::find(Session::get('id'))->auth_type;
        $out = [];
        foreach ($records as $record) {
            if($me < $record->id) $out = array_add($out, $record->id, $record->text);
        }
        return $out;
    }

    // end
}
