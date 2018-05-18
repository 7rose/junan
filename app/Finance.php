<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $table = 'finance';
    protected $fillable = ['customer_id','user_id','in','price','real_price','item', 'branch', 'date', 'created_by','content'];
}

