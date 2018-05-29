<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';
    protected $fillable = ['biz_id', 'lesson', 'ready', 'order_date', 'pass', 'score', 'import_id', 'user_id', 'created_by', 'content'];
}
