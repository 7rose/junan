<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarCostsTable extends Migration
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
            $table->integer('car_id');
            $table->integer('user_id');
            $table->integer('date');
            $table->integer('cost_type');
            $table->integer('cost_item');
            $table->integer('content')->nullable();
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
        Schema::dropIfExists('car_costs');
    }
}
