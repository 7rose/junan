<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use Session;

class CustomerSeekForm extends Form
{
    public function buildForm()
    {
        $this->add('key', 'text', [
            'label' => '关键词',
            'value' => Session::has('seek_array') ? Session::get('seek_array')['key'] : '',
            'rules' => 'min:1|max:16'
        ])
        ->add('submit','submit',[
              'label' => '查询',
              'attr' => ['class' => 'btn btn-info btn-block']
        ]);
    }
}
