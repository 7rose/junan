<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarIncome extends Model
{
    protected $table = 'car_incomes';
    protected $fillable = ['finance_id','car_id','start','hours','content','created_by'];
}