<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Helpers\ConfigList;

class StepForm extends Form
{
    public function buildForm()
    {
        $config_list = new ConfigList;

        $this
            ->add('branch', 'choice', [
                'label' => '所属驾校', 
                'empty_value' => '-- 选择 --',
                'choices'=> $config_list->branchList(),
                'rules' => 'required'
            ])
            ->add('lesson', 'choice', [
                'label' => '科目',
                'empty_value' => '-- 选择 --',
                'choices'=> [1=>1, 2=>2, 3=>3, 4=>4],
                'rules' => 'required'
            ])
            ->add('file', 'file', [
                'label' => 'Excel文件',
                'rules' => 'required'
            ]) 
            ->add('submit','submit',[
                  'label' => '校验并导入',
                  'attr' => ['class' => 'btn btn-success btn-block']
            ]);
    }
}
