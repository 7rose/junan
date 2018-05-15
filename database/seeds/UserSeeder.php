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
                'gender'=>2,
                'branch'=>1,
                'name'=>"钟艳",
                'mobile'=>"13815708136",
                'password'=>bcrypt('000000'),
                'root'=>true,
            ],[
                'work_id'=>1001,
                'gender'=>1,
                'branch'=>2,
                'name'=>"马军利",
                'mobile'=>"13800000000",
                'password'=>bcrypt('000000'),
                'root'=>false,
            ],
        ]);
    }
}
