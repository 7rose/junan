<?php

namespace App\Forms;

use Session;
use Kris\LaravelFormBuilder\Form;
use App\Helpers\ConfigList;
use App\Helpers\Auth;
use App\Biz;

class FinanceForm extends Form
{
    public function buildForm()
    {
        $list = new ConfigList;
        $auth = new Auth;

        $pre = Biz::where('biz.customer_id', $list->idFromUrl())
                        ->where('biz.finished', false)
                        ->leftJoin('config', 'biz.licence_type', 'config.id')
                        ->select('biz.id', 'config.text as licence_type_text');

        $records = $pre->get();   
        $first = $pre->first();   
        
        $this
            ->add('in', 'choice', [
                'label' => '收/付', 
                'empty_value' => '-- 选择 --',
                'choices'=> ['1'=>'收入+', '0'=>'支出-'],
                'rules' => 'required'
            ])
            ->add('item', 'choice', [
                'label' => '费用类型', 
                'empty_value' => '-- 选择 --',
                'choices'=> $list->getList('finance_item'),
                'rules' => 'required'
            ]);

        if(count($records) == 1) {
            $this->add('biz_id', 'hidden', ['value' => $first->id]);
        } elseif(count($records) > 1) {
            $tmp_list = [];
            foreach ($records as $record) {
                $tmp_list = array_add($tmp_list, $record->id, $record->licence_type_text);
            }

            $this->add('biz_id', 'choice', [
                'label' => '对应业务', 
                'empty_value' => '-- 选择 -- ',
                'choices'=> $tmp_list,
                'rules' => 'required'
            ]);
        }

        if($auth->branchLimit() || ($auth->admin() && Session::has('branch_set'))) {

            $this->add('branch', 'hidden', [
                'value' => $auth->branchLimitId()
            ]);
        }else{
            $this->add('branch', 'choice', [
                'label' => '所属驾校', 
                'empty_value' => '-- 选择 --',
                'choices'=> $list->branchList(),
                'rules' => 'required'
            ]);
        }

        if($auth->admin()) {
            $this->add('date', 'date', [
                'label' => '日期'
            ]);
        }
            $this->add('price', 'number', [
                    'label' => '应收/付',
                    'attr' =>['step' => 0.01],
                    'rules' => 'required'
            ])
            ->add('real_price', 'number', [
                    'label' => '实收/付',
                    'attr' =>['step' => 0.01],
                    'rules' => 'required'
            ])
            ->add('ticket_no', 'number', [
                'label' => '票据号',
                'rules' => 'required'
            ])
            ->add('user_id', 'text', [
                'label' => '推荐人工号',
                'label_attr' => ['id' => 'user_id_selector'],
                'attr' =>['readonly' => 'readonly', 'id'=>'user_id'],
                'rules' => 'required'
            ])
            // ->add('user_id', 'text', [
            //     'label' => '推荐人工号或手机号',
            //     'rules' => 'min:2|max:16'
            // ])
            ->add('customer_id', 'hidden', ['value' => $list->idFromUrl()])
            ->add('submit','submit',[
                  'label' => '提交',
                  'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
