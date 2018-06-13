<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Helpers\ConfigList;
use App\Helpers\Auth;
use Session;

class BizForm extends Form
{
    public function buildForm()
    {
        $list = new ConfigList;
        $auth = new Auth;
        
        $this->add('licence_type', 'choice', [
            'label' => '证照类型', 
            'empty_value' => '-- 选择 --',
            'choices'=> $list->getBizList($list->idFromUrl()),
        ])
        ->add('class_type', 'choice', [
            'label' => '班类型', 
            'empty_value' => '-- 选择 --',
            'choices'=> $list->getList('class_type'),
            'rules' => 'required'
        ])
        // ->add('date', 'date', [
        //     'label' => '报名日期',
        //     'rules' => 'required'
        // ])
        
        ->add('price', 'number', [
                'label' => '应收款',
                'attr' =>['step' => 0.01],
                'rules' => 'required'
        ])
        ->add('real_price', 'number', [
                'label' => '实收款',
                'attr' =>['step' => 0.01],
                'rules' => 'required'
        ])
        ->add('ticket_no', 'number', [
                'label' => '票据号',
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
            'choices'=> $list->branchList(),
            'rules' => 'required'
        ]);
    }

        $this->add('user_id', 'text', [
            'label' => '推荐人工号或手机号',
            'rules' => 'min:2|max:16'
        ])
        ->add('customer_id', 'hidden', ['value' => $list->idFromUrl()])
        ->add('submit','submit',[
              'label' => '提交',
              'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
