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
        $record = User::leftJoin('branches', 'users.branch', '=', 'branches.id')
                        ->select('users.locked', 'branches.show as branch_show')
                        ->find(Session::get('id'));

        $auth = new Auth;
        if($record->locked || !$auth->user() || !$record->branch_show){
            return redirect('/locked');
        }else{
            if($record->new && Request::path() != 'user/reset_password' && Request::path() != 'user/update_password'){
                return redirect('/user/reset_password');
            }else{
                return $next($request);
            }
        }
    }
}
