<?php

namespace App\Helpers;

use DB;

class Unique
{
    
    public function exists($val,$item,$table)
    {
        $first = DB::table($table)->where($item, $val)->first();
        return $first ? $first->id : false;
    }

    public function exists_id_number($val,$item,$table)
    {
        $first = DB::table($table)
                    ->where($item, strtolower($val))
                    ->orWhere($item, strtoupper($val))
                    ->first();
        return $first ? $first->id : false;
    }
}