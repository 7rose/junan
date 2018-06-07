<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $table = 'finance';
    protected $fillable = ['customer_id','user_id','in','price','real_price','item', 'branch', 'date', 'created_by','content','checked','checked_by','checked_by_time','checked_2','checked_2_by','checked_2_by_time','ticket_no'];
}
