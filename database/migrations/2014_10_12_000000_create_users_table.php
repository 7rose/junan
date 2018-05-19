<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('work_id');// 工号
            $table->string('mobile');// 手机
            // $table->string('id_number')->unique(); // 身份证
            $table->integer('branch'); // 驾校
            $table->integer('gender'); // 性别
            $table->string('name'); // 姓名
            $table->string('password'); // 密码
            $table->integer('user_type'); // 员工类型
            $table->integer('auth_type'); // 系统授权
            $table->decimal('finance_info',8,2)->nullable();
            $table->string('biz_info')->nullable(); // 业务信息
            $table->integer('created_by')->nullable(); // 创建人
            $table->boolean('locked')->default(false); // 锁定
            $table->boolean('new')->default(true); // 新用户
            $table->string('content')->nullable(); // 备注
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
