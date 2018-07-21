<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class CarCostForm extends Form
{
    public function buildForm()
    {
        $this->add('item', 'choice', [
            'label' => '支出类型', 
            'empty_value' => '-- 选择 --',
            'choices'=> ['33'=>'加油', '34'=>'维修'],
            'rules' => 'required'
        ])
        ->add('car_no', 'text', [
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
        ->add('date', 'date', [
            'label' => '日期', 
            'rules' => 'required'
        ])
        ->add('price', 'number', [
            'label' => '价格', 
            'attr' =>['step' => '0.01'],
            'rules' => 'required'
        ])
        ->add('content', 'textarea', [
            'label' => '备注(油量, 维修信息等)',
            'rules' => 'required'
        ])
        // ->add('path', 'hidden', ['value' => Request::path()])
        ->add('submit','submit',[
              'label' => '提交',
              'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
