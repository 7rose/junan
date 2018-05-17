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
                'text'=>"军安集团",
             ],[
                'parent_id'=> 1,
                'text'=>"军安(马厂)",
             ],[
                'parent_id'=> 1,
                'text'=>"恒通(沭城)",
             ],[
                'parent_id'=> 1,
                'text'=>"军越(汤涧)",
             ],[
                'parent_id'=> 1,
                'text'=>"鸿远(沭城)",
             ],[
                'parent_id'=> 1,
                'text'=>"军兴(周集)",
             ],[
                'parent_id'=> 1,
                'text'=>"军顺(沭城)",
             ],[
                'parent_id'=> 1,
                'text'=>"军畅(耿圩)",
             ],[
                'parent_id'=> 1,
                'text'=>"永安(沭城)",
             ],[
                'parent_id'=> 1,
                'text'=>"军盛(沂涛)",
             ],[
                'parent_id'=> 1,
                'text'=>"元顺(塘沟)",
             ],[
                'parent_id'=> 1,
                'text'=>"元安(章集)",
             ],[
                'parent_id'=> 1,
                'text'=>"杰达(北丁集)",
             ],
        ]);
    }
}


