<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
// use App\Helpers\ConfigList;

class CustomerForm extends Form
{
    public function buildForm()
    {
        $this->add('id_number', 'text', [
            'label' => '身份证号',
            'rules' => 'required|min:18|max:18|unique:customers'
        ])
        ->add('mobile', 'text', [
            'label' => '手机号',
            'rules' => 'required|min:11|max:11'
        ])
        ->add('name', 'text', [
            'label' => '姓名',
            'rules' => 'required|min:2|max:16'
        ])
        // ->add('gender', 'choice', [
        //     'label' => '性别', 
        //     'empty_value' => '-- 选择 --',
        //     'choices'=> ['1'=>'男', '2'=>'女'],
        //     'rules' => 'required'
        // ])
        ->add('address', 'text', [
            'label' => '身份证地址',
            'rules' => 'required|min:10|max:60'
        ])
        ->add('location', 'text', [
            'label' => '现居地 (选填)',
            'rules' => 'min:2|max:60'
        ])
        ->add('content', 'textarea', [
            'label' => '备注 (选填)',
            'rules' => 'min:2|max:200'
        ])
        ->add('submit','submit',[
              'label' => '提交',
              'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
