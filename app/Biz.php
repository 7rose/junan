<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Biz extends Model
{
    protected $table = 'biz';
    protected $fillable = ['customer_id','licence_type','class_type','date','user_id','class_id','file_id', 'branch', 'created_by','state','finished','locked','show'];
}

