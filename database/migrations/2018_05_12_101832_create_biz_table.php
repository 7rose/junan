<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('biz', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id'); // 用户id
            $table->integer('licence_type'); // 驾照类型
            $table->integer('class_type'); // 班类型
            $table->integer('date'); // 报名日期
            $table->integer('file_id')->nullable(); // 交管局开班档案号
            $table->integer('created_by')->nullable(); // 创建人
            $table->integer('state')->nullable(); // 状态
            $table->integer('locked')->default(false); // 状态
            $table->integer('show')->default(true); // 状态
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
        Schema::dropIfExists('biz');
    }
}
