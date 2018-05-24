<?php

namespace App\Helpers;

class Error
{
    
    // 无记录
    public function notFound()
    {
       return view('note')->with('custom', ['color'=>'danger', 'icon'=>'warning-sign', 'content'=>'无记录!']);
    }

    // 账号锁定
    public function locked()
    {
       return view('note')->with('custom', ['color'=>'warning', 'icon'=>'warning-sign', 'content'=>'您的账号被锁定, 请联系管理员!']);
    }

    public function forbidden()
    {
        return view('note')->with('custom', ['color'=>'warning', 'icon'=>'warning-sign', 'content'=>'无权继续操作!']);
    }

    public function paramLost()
    {
        return view('note')->with('custom', ['color'=>'danger', 'icon'=>'warning-sign', 'content'=>'缺少参数,可能是操作错误或者超时!']);
    }
}