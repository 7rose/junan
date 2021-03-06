<?php

Route::get('/', 'UserController@welcome');
Route::get('/login', 'UserController@login');
Route::get('/logout', 'UserController@logout');
Route::post('/user/check', ['as'=>'user.check', 'uses'=>'UserController@check']);
Route::get('/locked', 'UserController@lockInfo');

// 错误修复
Route::get('/fix', 'FixController@index');

// 日志
Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::group(['middleware' => ['login', 'state_check']], function () {
    // 日志
    Route::get('/biz_logs', 'LogsController@index');
    Route::get('/biz_logs/download/excel', 'LogsController@logsExcel');
    Route::post('/biz_logs/seek', 'LogsController@seek');

    // 文档
    Route::get('/doc', function() {
        return view('users.doc');
    });

    // 车辆管理
    Route::get('/cars', 'CarController@index');
    Route::get('/cars/incomes', 'CarController@incomeIndex');
    Route::get('/cars/incomes/excel', 'CarController@incomesExcel');
    Route::get('/cars/costs', 'CarController@costIndex');
    Route::get('/cars/costs/excel', 'CarController@costsExcel');
    Route::post('/cars/seek', 'CarController@seek');
    Route::get('/cars/seek/reset/{url}', 'CarController@resetSeek');
    Route::post('/cars/ajax/selector', 'CarController@selector');
    Route::get('/cars/income/create', 'CarController@income');
    Route::post('/cars/income/store', 'CarController@store');
    Route::get('/cars/cost/create', 'CarController@cost');
    Route::post('/cars/cost/store', 'CarController@costStore');
    Route::get('/cars/income_count/excel', 'CarController@countIncomesExcel');

    // 系统参数
    Route::get('/car', 'CarSetController@index');
    Route::get('/car/close/{id}', 'CarSetController@set');
    Route::post('/car/add', 'CarSetController@add');

    Route::get('/branch', 'BranchController@index');
    Route::get('/branch/close/{id}', 'BranchController@set');
    Route::post('/branch/add', 'BranchController@add');

    Route::get('/config/{key}', 'ConfigController@index');
    Route::get('/config/set/{key}', 'ConfigController@set');
    Route::post('/config/add/post', 'ConfigController@add');

    // 导入excel
    Route::get('/import/user', 'ImportController@userImport');
    Route::post('/import/user/store', ['as'=>'import.user_store', 'uses'=>'ImportController@userStore']);
    Route::get('/import/user/save', 'ImportController@userSave');
    Route::get('/import/class', 'ImportController@classImport');
    Route::post('/import/class/store', ['as'=>'import.class_store', 'uses'=>'ImportController@classStore']);
    Route::get('/import/class/save', 'ImportController@classSave');
    Route::get('/import/step', 'ImportController@step');
    Route::post('/import/step/store', 'ImportController@stepStore');
    Route::get('/import/step/save', 'ImportController@stepSave');

    // 学员
    Route::get('/customer', 'CustomerController@index');
    Route::get('/customer/create', 'CustomerController@create');
    Route::get('/customer/{id}', 'CustomerController@show');
    Route::post('/customer/store', ['as'=>'customer.store', 'uses'=>'CustomerController@store']);
    Route::post('/customer/seek', 'CustomerController@seek');
    Route::get('/customer/seek/reset', 'CustomerController@seekReset');
    Route::get('/customer/edit/{id}', 'CustomerController@edit');
    Route::post('/customer/update/{id}', ['as'=>'customer.update', 'uses'=>'CustomerController@update']);
    Route::get('/customer/download/excel', 'CustomerController@seekToExcel');

    // 员工
    Route::get('/user/doc', 'UserController@doc');
    Route::get('/user', 'UserController@index');
    Route::get('/user/create', 'UserController@create');
    Route::post('/user/store', ['as'=>'user.store', 'uses'=>'UserController@store']);
    Route::get('/user/password_help/{id}', 'UserController@passwordHelp');
    Route::get('/user/reset_password', 'UserController@resetPassword');
    Route::post('/user/update_password', ['as'=>'password.store', 'uses'=>'UserController@updatePassword']);
    Route::get('/user/{id}', 'UserController@show');
    Route::post('/user/seek', 'UserController@seek');
    Route::get('/user/seek/reset', 'UserController@seekReset');
    Route::get('/user/lock/{id}', 'UserController@lock');
    Route::get('/user/unlock/{id}', 'UserController@unlock');
    Route::get('/user/edit/{id}', 'UserController@edit');
    Route::post('/user/update/{id}', ['as'=>'user.update', 'uses'=>'UserController@update']);
    Route::get('/user/download/excel', 'UserController@seekToExcel');
    Route::get('/branch/set/{id}', 'UserController@setBranch');
    Route::post('/user/ajax/selector', 'UserController@selector');

    // 业务
    Route::post('/biz/claim', 'BizController@claim');
    Route::post('/biz/set_file_id', 'BizController@setFileId');
    Route::get('/biz/reprint/{id}', 'BizController@reprint');
    Route::get('/biz/file_id/cancel/{id}', 'BizController@cancelFileId');
    Route::get('/customer/biz/{id?}', 'BizController@create');
    Route::post('/customer/biz/store', ['as'=>'biz.store', 'uses'=>'BizController@store']);
    Route::get('/biz/edit/{id}', 'BizController@edit');
    Route::post('/biz/update/{id}', ['as'=>'biz.update', 'uses'=>'BizController@update']);
    Route::get('/biz/teacher/{key}', 'BizController@teacher');
    Route::get('/biz/close/{id}', 'BizController@close');
    Route::get('/biz/open/{id}', 'BizController@open');

    // 财务
    Route::get('/finance', 'FinanceController@index');
    Route::post('/finance/seek', 'FinanceController@seek');
    Route::get('/finance/seek/reset', 'FinanceController@seekReset');
    Route::get('/finance/create/{id}', 'FinanceController@create');
    Route::post('/finance/store/{id}', 'FinanceController@store');
    Route::get('/finance/edit/{id}', 'FinanceController@edit');
    Route::post('/finance/update/{id}', 'FinanceController@update');
    Route::get('/finance/download/excel', 'FinanceController@seekToExcel');
    Route::post('/finance/checking', 'FinanceController@checking');
    Route::post('/finance/check_2', 'FinanceController@check_2');
    Route::post('/finance/cancel', 'FinanceController@cancel');
    Route::post('/finance/abandon', 'FinanceController@abandon');

    // 考务过滤器
    Route::post('/filter/seek/set', 'FilterController@seek');
    Route::post('/filter/seek/reset', 'FilterController@seekReset');
    Route::post('/filter/select/{id}', 'FilterController@select');
    Route::post('/filter/cancel/{id}', 'FilterController@cancel');
    Route::get('/filter', 'FilterController@index');
    Route::any('/filter/{key}', 'FilterController@filter');
    Route::any('/filter/ex1/{key}', 'FilterController@ex1');
    Route::any('/filter/ex2/{key}', 'FilterController@ex2');
    Route::post('/filter/do/ready', 'FilterController@doReady');
    Route::post('/filter/do/date', 'FilterController@doDate');
    Route::get('/filter/do/score', 'FilterController@doScoreList');
    Route::post('/filter/save/score', 'FilterController@saveScore');
    Route::get('/filter/score_ex/{key}', 'FilterController@exScore');
    Route::get('/filter/3/pdf', 'FilterController@pdf');
    Route::get('/filter/download/excel', 'FilterController@filterToExcel');

    // 统计
    Route::any('/counter/post_set', 'CounterController@postSet');
    Route::get('/counter/set/{date}', 'CounterController@set');
    Route::get('/counter/finance', 'CounterController@finance');
    Route::get('/counter/finance/{id}', 'CounterController@financeShow');
    Route::get('/counter/finance/download/excel/{key}', 'CounterController@getExcel');
    Route::get('/counter/lesson/download/excel', 'CounterController@lessonExcel');
    Route::get('/counter/lesson', 'CounterController@lesson');
    Route::get('/counter/biz', 'CounterController@biz');
    Route::get('/counter/biz/download/excel', 'CounterController@bizExcel');
    Route::get('/filter/counter_finance_mode/{key}', 'CounterController@financeMode');

});


Route::get('/test', function() {
    // $c = new Carbon\Carbon;
});










