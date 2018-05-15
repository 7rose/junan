<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table="customers";

    protected $fillable = ['licence_type','class_type','name','gender','id_number','mobile','address','date','location','file_id','state','content','locked','show'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    // protected $hidden = [
    //     'password', 'remember_token',
    // ];
}

