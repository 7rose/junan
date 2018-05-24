<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
    protected $table = 'import';
    protected $fillable = ['table', 'user_id'];
}
