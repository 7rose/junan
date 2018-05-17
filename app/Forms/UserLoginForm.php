<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use Request;

class UserLoginForm extends Form
{
    public function buildForm()
    {
        // Add fields here...
        $this
            ->add('id', 'text', [
                'label' => '工号/手机号',
                'rules' => 'required|min:4|max:16'
            ])
            ->add('password', 'password', [
                'label' => '密码',
                'rules' => 'required|min:6|max:32'
            ])
            ->add('path', 'hidden', ['value' => Request::path()])
            //->add('path', 'hidden', ['value' => Request::url()])
            
            ->add('submit','submit',[
                  'label' => '提交',
                  'attr' => ['class' => 'btn btn-success btn-block']
            ]);
    }
}


