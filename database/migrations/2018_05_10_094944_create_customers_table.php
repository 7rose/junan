<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('licence_type'); // 驾照类型
            // $table->integer('class_type'); // 班类型
            $table->string('id_number')->unique(); // 身份证号
            $table->string('mobile'); // 电话
            $table->string('name'); // 姓名
            $table->integer('gender'); // 性别
            $table->string('address'); // 身份证地址
            $table->string('location')->nullable(); // 居住地
            // $table->integer('date'); // 报名日期
            // $table->integer('file_id')->nullable(); // 交管局开班档案号
            // $table->integer('state')->nullable(); // 状态
            $table->string('content')->nullable(); // 备注
            $table->boolean('locked')->default(false); // 锁定
            $table->boolean('show')->default(true); // 锁定
            $table->integer('created_by')->nullable(); // 性别
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
        Schema::dropIfExists('customers');
    }
}
