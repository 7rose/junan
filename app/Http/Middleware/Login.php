<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\UserLoginForm;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    use FormBuilderTrait;

    public function handle($request, Closure $next)
    {
        if(Session::has('id')) {
            return $next($request);
        }else{
            return redirect('/login');
        }
    }
}
