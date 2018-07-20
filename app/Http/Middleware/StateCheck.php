<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use Request;
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
        if(!Session::has('id') || Session::get('id') < 1) return redirect('/logout'); 

        $record = User::leftJoin('branches', 'users.branch', '=', 'branches.id')
                        ->select('users.locked', 'users.new', 'branches.show as branch_show')
                        ->find(Session::get('id'));
        if(!$record) return redirect('/logout'); 

        $auth = new Auth;
        if($record->locked || !$record->branch_show) return redirect('/locked');
        if(!$auth->user()) return redirect('/locked');
        if($record->new && Request::path() != 'user/reset_password' && Request::path() != 'user/update_password') return redirect('/user/reset_password');
        
        return $next($request);
    }
}
