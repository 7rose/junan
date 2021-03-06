<?php

namespace App\Helpers;

use App\User;
use Session;
use DB;

/**
 *  授权
 */
class Auth
{
    private $my_id;
    public $me;

    private $root_id; 
    private $admin_id; 
    private $finance_id;  # 财务 
    private $root_branch_id; # 总部
    private $info_id;

    function __construct()
    {
        // 配置root/admin的id
        $this->root_id = 4;
        $this->admin_id = 5;
        $this->user_id = 6;
        $this->root_branch_id = 1;
        $this->finance_id = 47;
        $this->info_id = 48;

        // 初始化
        $this->my_id = Session::get('id');
        $this->me = User::find(Session::get('id'));
    }

    // 有管理权
    public function master($target_id=0)
    {
        if($target_id==0) return false;

        $target_auth = User::find($target_id)->auth_type;
        return $this->me->auth_type < $target_auth ? true : false;
    }

    // 自己
    public function self($target_id)
    {
        return $this->my_id == $target_id ? true : false;
    }

    // 有待认领业务
    public function forCliam($target_id)
    {
        $has = DB::table('biz')
                    ->where('customer_id', $target_id)
                    ->where('branch',1)
                    ->where('finished', false)
                    ->get();
                    
        return count($has) ? true : false;
    }

    // 本机构有业务
    public function hasBiz($target_id)
    {
        $has_biz = DB::table('biz')
                    ->where('customer_id', $target_id)
                    ->where('branch',$this->me->branch)
                    ->where('finished', false)
                    ->get();
                    
        return count($has_biz) ? true : false;
    }

    // 同机构
    public function sameBranch($target_id)
    {
        if($this->admin()) return true;
        $target_branch = User::find($target_id)->branch;
        return $this->me->branch == $target_branch ? true : false;
    }

    // root - 超级管理员
    public function root($target_id=0)
    {
        if($target_id == 0) {
            return $this->me->auth_type == $this->root_id ? true : false;
        }else{
            $target_auth = User::find($target_id)->auth_type;
            return $target_auth == $this->root_id ? true : false;
        }
    }

    // admin - 管理员
    public function admin($target_id=0)
    {
        if($this->root($target_id)) return true;

        if($target_id == 0) {
            return $this->me->auth_type == $this->admin_id ? true : false;
        }else{
            $target_auth = User::find($target_id)->auth_type;
            return $target_auth == $this->admin_id ? true : false;
        }
    }

    // user - 用户
    public function user()
    {
        if($this->root() || $this->admin()) return true;
        return $this->me->auth_type == $this->user_id ? true : false;

    }

    // 财务
    public function finance()
    {
        if($this->root()) return true;
        return $this->me->user_type == $this->finance_id ? true : false;
    }

    // 财务主管
    public function financeMaster()
    {
        if($this->root()) return true;
        return $this->me->user_type == $this->finance_id && $this->me->branch == $this->root_branch_id ? true : false;
    }

    // 信息中心
    public function info()
    {
        if($this->root()) return true;
        return $this->me->user_type == $this->info_id ? true : false;
    }

    // 颜色
    public function authColor($auth_id)
    {
        switch ($auth_id) {
            case 4:
                return 'info';
                break;

            case 5:
                return 'primary';
                break;

            case 6:
                return 'success';
                break;
            
            default:
                return 'default';
                break;
        }
    }

    // 机构限制
    public function branchLimit()
    {
        return $this->admin() || $this->me->branch == $this->root_branch_id ? false : $this->me->branch;
    }

    // 获取机构代码
    public function branchLimitId()
    {
        if($this->branchLimit()) return $this->branchLimit();
        if(!$this->branchLimit() && Session::has('branch_set') && Session::get('branch_set') != 1) return Session::get('branch_set');
    }

    // end
}










