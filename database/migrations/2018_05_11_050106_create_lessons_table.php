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
            $table->integer('lesson_type')->nullable(); // 0-开班, 1-科目1 ...   
            $table->integer('user_id')->nullable(); // 教练员
            
            $table->boolean('ready')->nullable(); // 通过
            $table->boolean('pass')->nullable(); // 通过
            $table->integer('score')->nullable(); // 分数
            $table->integer('date')->nullable(); // 考试时间
            $table->string('location')->nullable(); // 地点
            $table->integer('import_id')->nullable(); // 导入批号
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
