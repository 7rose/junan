<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\User;

class StateCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locked = User::find(Session::get('id'))->locked;
        if($locked){
            return redirect('/locked');
        }else{
            return $next($request);
        }
    }
}
