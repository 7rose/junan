<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Car;

use App\Helpers\Auth;
use App\Helpers\Error;
use App\Helpers\ConfigList;
use App\Helpers\Logs;

class CarSetController extends Controller
{
    public function index()
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

       $records =  Car::leftJoin('config', 'cars.type', 'config.id')
                    ->select('cars.*', 'config.text as type_text')
                    ->orderBy('cars.created_at', 'desc')
                    ->get();

       $list = new ConfigList;
       $car_types = $list->getList('licence_type');

       return view('cars.car')
                    ->with('car_types', $car_types)
                    ->with('records', $records);
    }

    // 设置隐藏/显示
    public function set($id)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

        // if($id==1) return $auth_error->forbidden();

        $target = Car::find($id);

        $target->update(['show'=> $target->show ? false : true]);
        // 日志
        $log_content = "车辆: 启用/停止业务";
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return redirect('/car');
    }

    // 新机构
    public function add(Request $request)
    {
        // 授权
        $auth = new Auth;
        $auth_error = new Error;
        if(!$auth->root())  return $auth_error->forbidden();

        $type = $request->input('type');
        $car_no = $request->input('car_no');

        $check = Car::where('car_no', $car_no)->get();
        if(count($check)) return "车辆牌照重复";

        Car::create(['type'=>$type, 'car_no'=>$car_no]);

        // 日志
        $log_content = "车辆: 添加 - ".$car_no;
        $log_level = "danger";
        $log_put = new Logs;
        $log_put->put(['content'=>$log_content, 'level'=>$log_level]);

        return 200;
    }
}
