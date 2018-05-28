<?php

namespace App\Forms;

use Session;
use Kris\LaravelFormBuilder\Form;
use App\Helpers\ConfigList;
use App\Helpers\Auth;

class UserForm extends Form
{
    public function buildForm()
    {
        $config_list = new ConfigList;
        $auth = new Auth;

        $this->add('mobile', 'text', [
            'label' => '手机号',
            'rules' => 'required|min:11|max:11'
        ]);
        
        if(!$auth->self($config_list->idFromUrl()) || $auth->root()){
        $this
            ->add('name', 'text', [
                'label' => '姓名',
                'rules' => 'required|min:2|max:16'
            ])
            ->add('gender', 'choice', [
                'label' => '性别', 
                'empty_value' => '-- 选择 --',
                'choices'=> ['1'=>'男', '2'=>'女'],
                'rules' => 'required'
            ]);

        if($auth->branchLimit() || ($auth->admin() && Session::has('branch_set'))) {

            $this->add('branch', 'hidden', [
                'value' => $auth->branchLimitId()
            ]);
        }else{
            $this->add('branch', 'choice', [
                'label' => '所属驾校', 
                'empty_value' => '-- 选择 --',
                'choices'=> $config_list->branchList(),
                'rules' => 'required'
            ]);
        }

            $this->add('user_type', 'choice', [
                'label' => '用户类型', 
                'empty_value' => '-- 选择 --',
                'choices'=> $config_list->getList('user_type'),
                'rules' => 'required'
            ]);

            if(!$auth->self($config_list->idFromUrl())){
                $this->add('auth_type', 'choice', [
                        'label' => '权限', 
                        'empty_value' => '-- 选择 --',
                        'choices'=> $config_list->authList('auth_type'),
                        'rules' => 'required'
                ]);
            }
        }

        $this->add('content', 'textarea', [
            'label' => '备注 (选填)',
            'rules' => 'min:2|max:200'
        ])
        ->add('submit','submit',[
              'label' => '提交',
              'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
