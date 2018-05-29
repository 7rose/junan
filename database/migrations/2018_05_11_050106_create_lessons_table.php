<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('biz_id'); // 业务号
            $table->integer('lesson'); // 0-开班, 1-科目1 ...   
            $table->boolean('ready')->default(true); // 通过
            $table->integer('order_date')->nullable(); // 预约id
            $table->boolean('pass')->default(false); // 通过
            $table->boolean('end')->default(false); // 通过
            $table->boolean('doing')->default(false); // 通过
            $table->integer('score')->nullable(); // 分数
            $table->integer('import_id')->nullable(); // 导入批号
            $table->integer('user_id')->nullable(); // 教练员
            $table->integer('created_by')->nullable(); 
            $table->string('content')->nullable();
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
        Schema::dropIfExists('lessons');
    }
}
