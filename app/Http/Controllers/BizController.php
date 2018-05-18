<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

use Kris\LaravelFormBuilder\FormBuilderTrait;
use App\Forms\BizForm;
use App\Helpers\Error;
// use App\Helpers\Unique;
use App\Customer;
use App\Finance;
use App\Biz;
use App\User;

class BizController extends Controller
{
     use FormBuilderTrait;
    // 新业务
    public function create($id=0)
    {
        $error = new Error;

        if($id == 0) return redirect('/customer');
        $record = Customer::find($id);
        if(!$record) return $error->notFound();

        $form = $this->form(BizForm::class, [
            'method' => 'POST',
            'url' => route('biz.store')
        ]);

        $title = '新业务 - '.$record->name;
        $icon = 'credit-card';

        return view('form', compact('form'))->with('custom',['title'=>$title, 'icon'=>$icon]);
    }

    // 存储
    public function store(Request $request)
    {
        $all = $request->all();
        $all['date'] =  strtotime($all['date']);
        $all['created_by'] = Session::get('id');

        // 存储业务表
        $biz = array_except($all, ['price', 'real_price', 'user_id']);
        Biz::create($biz);

        // 财务表
        // 推荐人
        if($all['user_id'] != '' || $all['user_id'] != null){
            $exists = User::where('work_id', $all['user_id'])->orWhere('mobile', $all['user_id'])->first();
            if(!$exists) {
                $message = "工号或手机号不存在!";
                return redirect()->back()->withErrors(['user_id'=>$message])->withInput();
            }else{
                $all['user_id'] = $exists->id;
            }
        }

        $finance = array_except($all, ['licence_type', 'class_type']);
        $finance['item'] =  27;
        Finance::create($finance);
        return redirect('/customer/'.$all['customer_id']);
    }
}






