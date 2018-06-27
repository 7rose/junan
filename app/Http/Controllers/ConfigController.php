<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Config;
use App\Helpers\Config as Conf;
use App\Helpers\Auth;
use App\Helpers\Error;

class ConfigController extends Controller
{

    public function index($key)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

        $conf = new Conf;
        if(!array_has($conf->config_list, $key))  $auth_error->paramLost();

        $records = Config::where('type', $key)
                            ->orderBy('created_at', 'desc')
                            ->get();

        $target = ['key'=>$key, 'text'=>$conf->config_list[$key]];

        return view('config.index')
                    ->with('records', $records)
                    ->with('target', $target);


    }

    // 关闭和打开
    public function set(Request $request, $key)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

        $tmp = explode('-', $key);

        $target = Config::find($tmp[1]);
        $target->update(['show'=> $target->show ? false : true]);

        return redirect('/config/'.$tmp[0]);

    }

    // 添加
    public function add(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();
        
        $item = ['type' => $request->input('type'), 'text' => $request->input('text')];

        Config::create($item);
        return 'ok';
    }

    // end
}





















