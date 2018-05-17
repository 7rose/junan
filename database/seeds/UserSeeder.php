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
                'work_id'=>1000,
                'mobile'=>"13815708136",
                // 'id_number'=>"13815708136",
                'branch'=>1,
                'gender'=>2,
                'name'=>"钟艳",
                'password'=>bcrypt('000000'),
                'user_type'=>45,
                'auth_type'=>4,
                'created_by'=>1,
            ],[
                'work_id'=>1001,
                'mobile'=>"13811231226",
                // 'id_number'=>"13815708136",
                'branch'=>3,
                'gender'=>1,
                'name'=>"蓝凤凰",
                'password'=>bcrypt('000000'),
                'user_type'=>46,
                'auth_type'=>6,
                'created_by'=>1,
            ],
        ]);
    }
}

