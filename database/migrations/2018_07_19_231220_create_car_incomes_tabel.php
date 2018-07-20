<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarIncomesTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('finance_id'); // 财务记录
            $table->integer('car_id'); // 车辆记录
            $table->integer('branch');
            $table->integer('start'); // 开始时间
            $table->decimal('hours',6,2); // 数量
            $table->string('content')->nullable();
            $table->integer('created_by'); // 经手人
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
        Schema::table('car_incomes', function (Blueprint $table) {
            Schema::dropIfExists('car_incomes');
        });
    }
}
