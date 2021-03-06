<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'work_id'=>888,
                'mobile'=>"13305241588",
                // 'id_number'=>"13815708136",
                'branch'=>1,
                'gender'=>1,
                'name'=>"董事长",
                'password'=>bcrypt('888888'),
                'user_type'=>45,
                'auth_type'=>6,
                'created_by'=>3,
            ],[
                'work_id'=>999,
                'mobile'=>"18118088880",
                // 'id_number'=>"13815708136",
                'branch'=>1,
                'gender'=>1,
                'name'=>"总经理",
                'password'=>bcrypt('999999'),
                'user_type'=>45,
                'auth_type'=>6,
                'created_by'=>3,
            ],[
                'work_id'=>1000,
                'mobile'=>"13815708136",
                // 'id_number'=>"13815708136",
                'branch'=>1,
                'gender'=>2,
                'name'=>"管理员",
                'password'=>bcrypt('000000'),
                'user_type'=>46,
                'auth_type'=>4,
                'created_by'=>3,
            ],[
                'work_id'=>1001,
                'mobile'=>"15371373940",
                // 'id_number'=>"13815708136",
                'branch'=>1,
                'gender'=>2,
                'name'=>"助理",
                'password'=>bcrypt('10011001'),
                'user_type'=>47,
                'auth_type'=>6,
                'created_by'=>3,
            ],
        ]);
    }
}

