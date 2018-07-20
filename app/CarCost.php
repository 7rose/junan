<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarCost extends Model
{
    protected $table = 'car_costs';
    protected $fillable = ['finance_id','car_id','content','created_by'];
}
