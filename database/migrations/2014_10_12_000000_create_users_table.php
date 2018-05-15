<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('work_id')->unique();
            $table->integer('branch');
            $table->integer('gender');
            $table->string('name');
            $table->string('mobile')->unique();
            $table->string('password');
            $table->boolean('root')->default(false);
            $table->boolean('admin')->default(false);
            $table->boolean('use')->default(true);
            $table->boolean('locked')->default(false);
            $table->boolean('new')->default(true);
            $table->string('content')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
