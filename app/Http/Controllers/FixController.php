<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Helpers\Pre;

class FixController extends Controller
{
    public function index()
    {
        $this->updateFinance();
        echo "Fixed";
    }

    public function updateFinance()
    {
        // 更新预处理财务结果数据
        $pre = new Pre;
        $pre->updateFinance();
    }

    
}
