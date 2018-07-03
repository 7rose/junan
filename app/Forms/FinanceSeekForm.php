<?php

namespace App\Forms;

use Session;
use App\Helpers\ConfigList;

use Kris\LaravelFormBuilder\Form;

class FinanceSeekForm extends Form
{
    public function buildForm()
    {
        $config_list = new ConfigList;

        $this->add('key', 'text', [
            'label' => '关键词',
            'value' => Session::has('seek_array') ? Session::get('seek_array')['key'] : '',
            'rules' => 'min:1|max:16'
        ])
        // ->add('branch', 'choice', [
        //     'label' => '所属驾校', 
        //     'selected' => Session::has('finance_seek_array') && Session::get('finance_seek_array')['branch']!='' ? Session::get('finance_seek_array')['branch'] : '',
        //     'empty_value' => '-- 选择 --',
        //     'choices'=> $config_list->branchList(),
        // ])
        ->add('date_begin', 'date', [
            'label' => '时间: 起点, 含当日',
            'value' => Session::has('finance_seek_array') && Session::get('finance_seek_array')['date_begin']!='' ? Session::get('finance_seek_array')['date_begin'] : '',
        ])
        ->add('date_end', 'date', [
            'label' => '时间: 终点, 不含当日',
            'value' => Session::has('finance_seek_array') && Session::get('finance_seek_array')['date_end']!='' ? Session::get('finance_seek_array')['date_end'] : '',
        ])
        ->add('submit','submit',[
              'label' => '查询',
              'attr' => ['class' => 'btn btn-info btn-block']
        ]);
    }
}
