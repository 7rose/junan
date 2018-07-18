<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

use App\Helpers\ConfigList;
use Request;

class BizEditForm extends Form
{
    public function buildForm()
    {
        $list = new ConfigList;
        
        $this->add('licence_type', 'choice', [
            'label' => '证照类型', 
            'empty_value' => '-- 选择 --',
            // 'choices'=> $list->getBizList($list->idFromUrl()),
            'choices'=> $list->getList('licence_type'),
            'rules' => 'required'
        ])
            ->add('class_type', 'choice', [
                'label' => '班类型', 
                'empty_value' => '-- 选择 --',
                'choices'=> $list->getList('class_type'),
                'rules' => 'required'
            ])
            ->add('branch', 'choice', [
                'label' => '所属驾校', 
                'empty_value' => '-- 选择 --',
                'choices'=> $list->branchList(),
                'rules' => 'required'
            ])
            ->add('path', 'hidden', ['value' => Request::path()])
            ->add('submit','submit',[
                  'label' => '提交',
                  'attr' => ['class' => 'btn btn-success btn-block']
            ]);
    }
}
