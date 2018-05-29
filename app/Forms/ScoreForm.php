<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class ScoreForm extends Form
{
    public function buildForm()
    {
        $this->add('order_date', 'date', [
            'label' => '考试日期',
            'rules' => 'required'
        ])

        ->add('lesson', 'choice', [
            'label' => '科目', 
            'empty_value' => '-- 选择 --',
            'choices'=> ['1'=>'科目1', '2'=>'科目2', '3'=>'科目3', '4'=>'科目4'],
            'rules' => 'required'
        ])
        ->add('submit','submit',[
              'label' => '提交',
              'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
