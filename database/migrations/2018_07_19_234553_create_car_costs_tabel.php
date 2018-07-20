<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCostsTabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_costs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('finance_id'); // 财务记录
            $table->integer('car_id'); // 车辆记录
            $table->integer('branch'); // 车辆记录
            // $table->integer('type'); // 开支类型
            // $table->integer('unit'); // 单位
            // $table->decimal('num',8,2)->nullable(); // 数量
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
        Schema::table('car_costs', function (Blueprint $table) {
            Schema::dropIfExists('car_costs');
        });
    }
}
