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
                'name'=>"军安集团",
             ],[
                'parent_id'=> 1,
                'name'=>"军安(马厂)",
             ],[
                'parent_id'=> 1,
                'name'=>"恒通(沭城)",
             ],[
                'parent_id'=> 1,
                'name'=>"军越(汤涧)",
             ],[
                'parent_id'=> 1,
                'name'=>"鸿远(沭城)",
             ],[
                'parent_id'=> 1,
                'name'=>"军兴(周集)",
             ],[
                'parent_id'=> 1,
                'name'=>"军顺(沭城)",
             ],[
                'parent_id'=> 1,
                'name'=>"军畅(耿圩)",
             ],[
                'parent_id'=> 1,
                'name'=>"永安(沭城)",
             ],[
                'parent_id'=> 1,
                'name'=>"军盛(沂涛)",
             ],[
                'parent_id'=> 1,
                'name'=>"元顺(塘沟)",
             ],[
                'parent_id'=> 1,
                'name'=>"元安(章集)",
             ],[
                'parent_id'=> 1,
                'name'=>"杰达(北丁集)",
             ],
        ]);
    }
}


