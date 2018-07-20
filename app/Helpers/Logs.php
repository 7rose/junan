<?php

namespace App\Helpers;

use DB;
use Session;
use Request;
use Log;
use App\Logs as BizLog;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * 业务日志
 */
class Logs
{
    // function __construct()
    // {
    //     if (!Schema::hasTable('logs')) {
    //         Schema::create('logs', function (Blueprint $table) {
    //             $table->increments('id');
    //             $table->string('level');
    //             $table->integer('who');
    //             $table->string('from');
    //             $table->string('content')->nullable();
    //             $table->timestamps();
    //         });
    //     }
    // }

    // 写日志
    public function put($array)
    {
        if(!isset($array['content'])) {
            Log::emergency('业务日志错误!');
            exit;
        }

        if(!isset($array['level']) || (isset($array['level']) && $array['level'] != 'info' && $array['level'] != 'warning' && $array['level'] != 'danger')) $array['level'] = 'info';
        $array['who'] = Session::get('id');

        $ip = Request::ip();
        $array['from'] = $ip.'|'.$this->ip2Address($ip);

        // DB::table('logs')->create($array);
        BizLog::create($array);
    }

    // ip转换
    public function ip2Address($ip)
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $data = json_decode(file_get_contents($url), true)['data'];  
        return $data['country'].'/'.$data['region'].'/'.$data['city']; 
    }
}

