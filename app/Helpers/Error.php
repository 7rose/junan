<?php

namespace App\Helpers;

class Error
{
    
    // 无记录
    public function notFound()
    {
       return view('note')->with('custom', ['color'=>'danger', 'icon'=>'warning-sign', 'content'=>'无记录!']);
    }
}