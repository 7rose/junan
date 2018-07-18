<?php

namespace App\Helpers;

use DB;
use Session;
use Request;
use Log;

/**
 * 业务日志
 */
class Logs
{
    // 写日志
    public function put($array)
    {
        if(!isset($array['content'])) {
            Log::emergency('业务日志错误!');
        }

        if(!isset($array['level'])) $array['level'] = 'info';
        $array['who'] = Session::get('id');
        $array['from'] = $this->ip2Address(Request::ip());

        print_r($array);

        // DB::table('logs')->create($array);
    }

    // ip转换
    public function ip2Address($ip)
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $data = json_decode(file_get_contents($url), true)['data'];  

        return $data['country'].'/'.$data['region'].'/'.$data['city']; 
        // return $data;
    }
}