<?php

namespace App\Helpers;
/**
 * 项目配置
 */
class Config
{
    
    public $config_list;

    function __construct()
    {
        $this->config_list = [
            'finance_item'=>'财务收费名目', 
            'lesson_fall_item'=>'科目2扣分项'
        ];
    }

    public function confMenu()
    {
        $menus = '';

        if(count($this->config_list)) {
            foreach ($this->config_list as $key => $value) {
                $menus .= '<li><a href="/config/'.$key.'">&nbsp&nbsp'.$value.'</a></li>';
            }
        }

        return $menus;
    }

    // end
}