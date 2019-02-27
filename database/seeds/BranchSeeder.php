<?php

use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('branches')->insert([
            [
                'parent_id'=> 0,
                'text'=>"银杏集团",
             ],[
                'parent_id'=> 1,
                'text'=>"银安",
             ],[
                'parent_id'=> 1,
                'text'=>"恒通",
             ],[
                'parent_id'=> 1,
                'text'=>"银越",
             ],[
                'parent_id'=> 1,
                'text'=>"鸿远",
             ],[
                'parent_id'=> 1,
                'text'=>"银兴",
             ],
        ]);
    }
}


