<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table="customers";

    protected $fillable = ['licence_type','class_type','name','gender','id_number','mobile','address','date','finance_info', 'biz_info', 'location','file_id','state', 'created_by','content','locked','show'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];
}

