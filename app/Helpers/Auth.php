<?php

namespace App\Helpers;

use App\User;
use Session;

/**
 *  授权
 */
class Auth
{
    private $my_id;
    private $my_auth; 
    private $root_id; 
    private $admin_id; 
    // private $user_id; 

    function __construct()
    {
        // 配置root/admin的id
        $this->root_id = 4;
        $this->admin_id = 5;
        $this->user_id = 6;

        // 初始化
        $this->my_id = Session::get('id');
        $this->my_auth = User::find($this->my_id)->auth_type;
    }

    // 有管理权
    public function master($target_id=0)
    {
        if($target_id==0) return false;

        $target_auth = User::find($target_id)->auth_type;
        return $this->my_auth < $target_auth ? true : false;
    }

    // 自己
    public function self($target_id)
    {
        return $this->my_id == $target_id ? true : false;
    }

    // root - 超级管理员
    public function root($target_id=0)
    {
        if($target_id == 0) {
            return $this->my_auth == $this->root_id ? true : false;
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
            return $this->my_auth == $this->admin_id ? true : false;
        }else{
            $target_auth = User::find($target_id)->auth_type;
            return $target_auth == $this->admin_id ? true : false;
        }
    }

    // user - 用户
    public function user()
    {
        if($this->root() || $this->admin()) return true;
        return $this->my_auth == $this->user_id ? true : false;

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

    // end
}










