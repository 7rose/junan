<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finance', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->index();
            $table->integer('user_id')->nullable();
            $table->integer('branch')->nullable();
            $table->integer('in')->default(true);
            $table->decimal('price',8,2)->nullable();
            $table->decimal('real_price',8,2);
            $table->integer('item');
            $table->integer('date')->nullable();
            $table->integer('created_by')->nullable(); // 创建人
            $table->string('content')->nullable();

            $table->boolean('checked')->default(false);
            $table->integer('checked_by')->nullable();
            $table->integer('checked_by_time')->nullable();
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
        Schema::dropIfExists('finance');
    }
}
