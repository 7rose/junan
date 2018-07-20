<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;

use App\Helpers\Auth;
use App\Helpers\Error;
use App\Helpers\ConfigList;
use App\Helpers\Logs;

class CarController extends Controller
{
    private $auth;

    private function pre() 
    {
        $this->auth = new Auth;
        // // 授权
        // $auth = new Auth;
        // $auth_error = new Error;
        // if(!$auth->root())  return $auth_error->forbidden();

        // 若财务中废弃,则支出中废弃
        $pre =  DB::table('car_incomes')
                        ->leftJoin('branches', 'car_incomes.branch', 'branches.id')
                        ->leftJoin('finance', 'car_incomes.finance_id', 'finance.id')
                        ->leftJoin('users', 'finance.user_id', 'users.id')
                        ->leftJoin('cars', 'car_incomes.car_id', 'cars.id')
                        ->leftJoin('config', 'cars.type', 'config.id')
                        ->where(function ($query) {
                                // 分支机构限制
                                if($this->auth->branchLimit() || (!$this->auth->branchLimit() && Session::has('branch_set')  && Session::get('branch_set') != 1)) {
                                    $query->Where('car_incomes.branch', $this->auth->branchLimitId());
                                }
                                 
                                // // 起时间点
                                // if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_begin') && Session::get('finance_seek_array')['date_begin'] != '') {
                                //     $query->Where('finance.date', '>=', strtotime(Session::get('finance_seek_array')['date_begin']));
                                // }

                                // // 终时间点
                                // if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'date_end') && Session::get('finance_seek_array')['date_end'] != '') {
                                //     $query->Where('finance.date', '<=', strtotime(Session::get('finance_seek_array')['date_end']));
                                // }

                                // // 关键词
                                // if(Session::has('finance_seek_array') && array_has(Session::get('finance_seek_array'), 'key') && Session::get('finance_seek_array')['key'] != '') {
                                //     $query->Where('finance.price', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('finance.real_price', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('customers.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('c.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('u.name', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                //     $query->orWhere('i.text', 'LIKE', '%'.Session::get('finance_seek_array')['key'].'%');
                                // }
                            });
        return $pre;
    }

    // 列表
    public function index()
    {
        $records = $this->pre()->latest('car_incomes.created_at')->get();
        // print_r($records);
        return view('cars.main');
    }

    // 添加


    // end
}








