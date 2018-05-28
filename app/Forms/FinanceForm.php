<?php

namespace App\Forms;

use Session;
use Kris\LaravelFormBuilder\Form;
use App\Helpers\ConfigList;
use App\Helpers\Auth;

class FinanceForm extends Form
{
    public function buildForm()
    {
        $list = new ConfigList;
        $auth = new Auth;
        
        $this
            ->add('in', 'choice', [
                'label' => '收/付', 
                'empty_value' => '-- 选择 --',
                'choices'=> ['1'=>'收入+', '2'=>'支出-'],
                'rules' => 'required'
            ])
            ->add('item', 'choice', [
                'label' => '费用类型', 
                'empty_value' => '-- 选择 --',
                'choices'=> $list->getList('finance_item'),
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
                'choices'=> $config_list->branchList(),
                'rules' => 'required'
            ]);
        }
            $this->add('date', 'date', [
                'label' => '日期',
                'rules' => 'required'
            ])
            ->add('price', 'number', [
                    'label' => '应收/付',
                    'attr' =>['step' => 0.01],
                    'rules' => 'required'
            ])
            ->add('real_price', 'number', [
                    'label' => '实收/付',
                    'attr' =>['step' => 0.01],
                    'rules' => 'required'
            ])
            ->add('user_id', 'text', [
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
