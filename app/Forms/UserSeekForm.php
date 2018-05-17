<?php

namespace App\Forms;
use App\Helpers\ConfigList;

use Kris\LaravelFormBuilder\Form;
use Session;

class UserSeekForm extends Form
{
    public function buildForm()
    {
        $config_list = new ConfigList;

        $this->add('key', 'text', [
            'label' => '关键词',
            'value' => Session::has('user_seek_array') && Session::get('user_seek_array')['key'] != '' ? Session::get('user_seek_array')['key'] : '',
            'rules' => 'min:1|max:16'
        ])
        ->add('branch', 'choice', [
            'label' => '所属驾校', 
            'selected' => Session::has('user_seek_array') && Session::get('user_seek_array')['branch']!='' ? Session::get('user_seek_array')['branch'] : '',
            'empty_value' => '-- 选择 --',
            'choices'=> $config_list->branchList(),
        ])
        ->add('submit','submit',[
              'label' => '查询',
              'attr' => ['class' => 'btn btn-info btn-block']
        ]);
    }
}
