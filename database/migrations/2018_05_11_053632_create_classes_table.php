<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('branch'); // 驾校
            $table->integer('class_no'); // 开班号
            $table->integer('licence_type'); // 驾照类型
            $table->integer('date')->nullable(); // 开班日期
            $table->integer('created_by')->nullable(); // 操作人
            $table->string('content')->nullable(); // 开班号
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
        Schema::dropIfExists('classes');
    }
}
