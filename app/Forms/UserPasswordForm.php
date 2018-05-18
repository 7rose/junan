<?php

namespace App\Forms;

use Kris\LaravelFormBuilder\Form;

class UserPasswordForm extends Form
{
    public function buildForm()
    {
        $this->add('password', 'password', [
            'label' => '新密码',
            'rules' => 'required|min:4|max:32'
        ])
        ->add('password_confirmed', 'password', [
            'label' => '确认新密码',
            'rules' => 'required|min:4|max:32'
        ])
        ->add('submit','submit',[
              'label' => '确定修改',
              'attr' => ['class' => 'btn btn-success btn-block']
        ]);
    }
}
