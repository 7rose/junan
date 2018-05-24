<?php

use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('config')->insert([
            [
                'type'    => 'gender',
                'text'    => '男',
                'extra'   => NULL,
            ],[
                'type'    => 'gender',
                'text'    => '女',
                'extra'   => NULL,
            ],[
                'type'    => 'gender',
                'text'    => '其他',
                'extra'   => NULL,
            ],[
                'type'    => 'auth_type',
                'text'    => '超级管理员',
                'extra'   => NULL,
            ],[
                'type'    => 'auth_type',
                'text'    => '管理员',
                'extra'   => NULL,
            ],[
                'type'    => 'auth_type',
                'text'    => '用户',
                'extra'   => NULL,
            ],[
                'type'    => 'auth_type',
                'text'    => '员工',
                'extra'   => NULL,
            ],[
                'type'    => 'customer_state',
                'text'    => '报名成功',
                'extra'   => NULL,
            ],[
                'type'    => 'customer_state',
                'text'    => '开班成功',
                'extra'   => NULL,
            ],[
                'type'    => 'customer_state',
                'text'    => '退学',
                'extra'   => NULL,
            ],[
                'type'    => 'customer_state',
                'text'    => '过期',
                'extra'   => NULL,
            ],[
                'type'    => 'customer_state',
                'text'    => '拿证',
                'extra'   => NULL,
            ],[
                'type'    => 'customer_state',
                'text'    => '其他',
                'extra'   => NULL,
            ],[
                'type'    => 'licence_type',
                'text'    => 'A1: 大型客车',
                'extra'   => '26-50',
            ],[
                'type'    => 'licence_type',
                'text'    => 'A2: 牵引车',
                'extra'   => '24-50',
            ],[
                'type'    => 'licence_type',
                'text'    => 'B2: 大型货车',
                'extra'   => '20-50',
            ],[
                'type'    => 'licence_type',
                'text'    => 'C1: 小型汽车',
                'extra'   => '18-70',
            ],[
                'type'    => 'licence_type',
                'text'    => 'C2: 小型自动挡汽车',
                'extra'   => '18-70',
            ],[
                'type'    => 'licence_type',
                'text'    => '资格证: 货运',
                'extra'   => NULL,
            ],[
                'type'    => 'licence_type',
                'text'    => '资格证: 客运',
                'extra'   => NULL,
            ],[
                'type'    => 'licence_type',
                'text'    => '资格证: 出租车',
                'extra'   => NULL,
            ],[
                'type'    => 'licence_type',
                'text'    => '其他',
                'extra'   => NULL,
            ],[
                'type'    => 'class_type',
                'text'    => '普通班',
                'extra'   => NULL,
            ],[
                'type'    => 'class_type',
                'text'    => '高级班',
                'extra'   => NULL,
            ],[
                'type'    => 'class_type',
                'text'    => 'VIP',
                'extra'   => NULL,
            ],[
                'type'    => 'class_type',
                'text'    => '至尊VIP',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '学费',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '补考费',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '制卡费',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '租车费',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '换办证类型',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '换班型',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '陪练费',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '保险',
                'extra'   => NULL,
            ],[
                'type'    => 'finance_item',
                'text'    => '购车',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson',
                'text'    => '科目1',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson',
                'text'    => '科目2',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson',
                'text'    => '科目3',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson',
                'text'    => '科目4',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson_state',
                'text'    => '报名成功',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson_state',
                'text'    => '通过',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson_state',
                'text'    => '未通过',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson_state',
                'text'    => '缺考',
                'extra'   => NULL,
            ],[
                'type'    => 'lesson_state',
                'text'    => '其他',
                'extra'   => NULL,
            ],[
                'type'    => 'user_type',
                'text'    => '决策',
                'extra'   => NULL,
            ],[
                'type'    => 'user_type',
                'text'    => '管理',
                'extra'   => NULL,
            ],[
                'type'    => 'user_type',
                'text'    => '财务',
                'extra'   => NULL,
            ],[
                'type'    => 'user_type',
                'text'    => '教练',
                'extra'   => NULL,
            ],[
                'type'    => 'user_type',
                'text'    => '业务',
                'extra'   => NULL,
            ],[
                'type'    => 'user_type',
                'text'    => '其他',
                'extra'   => NULL,
            ],
        ]);
    }
}
