<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Request;

class Login
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
        if(Session::has('id') && Session::get('id') >= 1) {
            return $next($request);
        }else{
            $url = Request::fullUrl();
            Session::put('target_url', $url);

            return redirect()->action('UserController@login');
        }
    }
}
