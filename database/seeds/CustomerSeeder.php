<?php

use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->insert([
            [
                'id_number'=>'320826198001057057',
                'mobile'=>'13657107711',
                'name'=>'令狐冲',
                'gender'=>1,
                'address'=>"华山市区五岳区108号",
                'location'=>'恒山市区',
            ],[
                'id_number'=>'320826195201157057',
                'mobile'=>'13657107712',
                'name'=>'风清扬',
                'gender'=>1,
                'address'=>"华山市区五岳区109号",
                'location'=>'华山山洞',
            ],[
                'id_number'=>'320826200201237057',
                'mobile'=>'13657107715',
                'name'=>'任盈盈',
                'gender'=>2,
                'address'=>"华山市区五岳区108号",
                'location'=>'恒山市区',
            ]
        ]);
    }
}