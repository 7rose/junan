<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('car_no'); // 牌照
            $table->integer('type'); // 车型
            $table->boolean('show')->default(true); // 车型
            $table->integer('branch')->default(1); // 所在驾校
            $table->string('content')->nuallable();
            $table->integer('created_by'); // 所在驾校
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
        Schema::dropIfExists('cars');
    }
}
