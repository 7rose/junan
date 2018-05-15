<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Helpers\ConfigList;

class BizForm extends Form
{
    public function buildForm()
    {
        $list = new ConfigList;
        
        $this->add('licence_type', 'choice', [
            'label' => '驾照类型', 
            'empty_value' => '-- 选择 --',
            'choices'=> $list->getList('licence_type'),
            'rules' => 'required'
        ])
        ->add('class_type', 'choice', [
            'label' => '班类型', 
            'empty_value' => '-- 选择 --',
            'choices'=> $list->getList('class_type'),
            'rules' => 'required'
        ])
        ->add('date', 'date', [
            'label' => '报名日期',
            'rules' => 'required'
        ]);
    }
}
