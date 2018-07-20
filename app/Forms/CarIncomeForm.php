<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class CarIncomeForm extends Form
{
    public function buildForm()
    {
        // $list = new ConfigList;
        
        $this->add('car_no', 'text', [
            'label' => '牌号', 
            'label_attr' => ['id' => 'car_no_selector'],
            'attr' =>['readonly' => 'readonly', 'id'=>'car_no'],
            'rules' => 'required'
        ])
        ->add('user_id', 'text', [
            'label' => '教练', 
            'label_attr' => ['id' => 'user_id_selector'],
            'attr' =>['readonly' => 'readonly', 'id'=>'user_id'],
            'rules' => 'required'
        ])
        ->add('ticket_no', 'number', [
            'label' => '票号', 
            'rules' => 'required'
        ])
        ->add('start', 'datetime-local', [
            'label' => '开始日期和时间', 
            'rules' => 'required'
        ])
        ->add('hours', 'number', [
            'label' => '小时数', 
            'attr' =>['step' => '0.5'],
            'rules' => 'required'
        ])
        ->add('price', 'number', [
            'label' => '价格', 
            'attr' =>['step' => '0.01'],
            'rules' => 'required'
        ])
        ->add('content', 'textarea', [
            'label' => '备注'
        ])
        // ->add('path', 'hidden', ['value' => Request::path()])
        ->add('submit','submit',[
              'label' => '提交',
              'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
