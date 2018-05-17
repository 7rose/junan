<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use App\User;
use App\Helpers\Auth;

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
        $auth = new Auth;

        if($locked || !$auth->user()){
            return redirect('/locked');
        }else{
            return $next($request);
        }
    }
}
