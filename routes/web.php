<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'UserController@welcome');
Route::get('/login', 'UserController@login');
Route::get('/logout', 'UserController@logout');
Route::post('/user/check', ['as'=>'user.check', 'uses'=>'UserController@check']);
Route::get('/locked', 'UserController@lockInfo');

Route::group(['middleware' => ['login', 'state_check']], function () {
    // doc
    Route::get('/doc', function() {
        return view('users.doc');
    });
    // 学员
    Route::get('/customer', 'CustomerController@index');
    Route::get('/customer/create', 'CustomerController@create');
    Route::get('/customer/{id}', 'CustomerController@show');
    Route::post('/customer/store', ['as'=>'customer.store', 'uses'=>'CustomerController@store']);
    Route::post('/customer/seek', ['as'=>'customer.seek', 'uses'=>'CustomerController@seek']);
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
    Route::post('/user/seek', ['as'=>'user.seek', 'uses'=>'UserController@seek']);
    Route::get('/user/seek/reset', 'UserController@seekReset');
    Route::get('/user/lock/{id}', 'UserController@lock');
    Route::get('/user/unlock/{id}', 'UserController@unlock');
    Route::get('/user/edit/{id}', 'UserController@edit');
    Route::post('/user/update/{id}', ['as'=>'user.update', 'uses'=>'UserController@update']);
    Route::get('/user/download/excel', 'UserController@seekToExcel');
    Route::get('/branch/set/{id}', 'UserController@setBranch');

    // 业务
    Route::post('/biz/claim', 'BizController@claim');
    Route::get('/customer/biz/{id?}', 'BizController@create');
    Route::post('/customer/biz/store', ['as'=>'biz.store', 'uses'=>'BizController@store']);
    Route::get('/biz/edit/{id}', 'BizController@edit');
    Route::post('/biz/update/{id}', ['as'=>'biz.update', 'uses'=>'BizController@update']);
    Route::get('/biz/teacher/{key}', 'BizController@teacher');
    Route::get('/biz/close/{id}', 'BizController@close');
    Route::get('/biz/open/{id}', 'BizController@open');
    // Route::post('/biz/update/{id}', ['as'=>'biz.update', 'uses'=>'BizController@update']);

    // 财务
    Route::get('/finance', 'FinanceController@index');
    Route::post('/finance/seek', ['as'=>'finance.seek', 'uses'=>'FinanceController@seek']);
    Route::get('/finance/seek/reset', 'FinanceController@seekReset');
    Route::get('/finance/create/{id}', 'FinanceController@create');
    Route::post('/finance/store', ['as'=>'finance.store', 'uses'=>'FinanceController@store']);
    Route::get('/finance/download/excel', 'FinanceController@seekToExcel');
    Route::post('/finance/checking', 'FinanceController@checking');
    Route::post('/finance/check_2', 'FinanceController@check_2');
    Route::post('/finance/cancel', 'FinanceController@cancel');
    Route::post('/finance/abandon', 'FinanceController@abandon');

    // 导入 -用户
    Route::get('/import/user', 'ImportController@userImport');
    Route::post('/import/user/store', ['as'=>'import.user_store', 'uses'=>'ImportController@userStore']);
    Route::get('/import/user/save', 'ImportController@userSave');
    // 导入 -开班花名册
    Route::get('/import/class', 'ImportController@classImport');
    Route::post('/import/class/store', ['as'=>'import.class_store', 'uses'=>'ImportController@classStore']);
    Route::get('/import/class/save', 'ImportController@classSave');

    // 过滤器
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
    // Route::post('/filter/do/score', 'FilterController@doScore');
    // Route::get('/filter/score/choose', 'FilterController@scoreChoose');
    // Route::post('/filter/do/score', ['as'=>'score.do', 'uses'=>'FilterController@doScore']);
    Route::get('/filter/do/score', 'FilterController@doScoreList');
    Route::post('/filter/save/score', 'FilterController@saveScore');

    Route::get('/filter/score_ex/{key}', 'FilterController@exScore');


    // Route::post('/filter/part', 'FilterController@ex');
    // Route::post('/filter/score/ex', ['as'=>'score.ex', 'uses'=>'FilterController@score_ex']);
    // Route::post('/filter/score_ex/save', 'FilterController@score_save');
    // Route::post('/filter/ready/ex', 'FilterController@readyEx');
    // Route::post('/filter/date/ex', 'FilterController@dateEx');
    Route::get('/filter/download/excel', 'FilterController@filterToExcel');

    // 统计
    Route::get('/counter/finance', 'CounterController@finance');
    Route::get('/counter/finance/{id}', 'CounterController@financeShow');
    Route::get('/counter/finance/set/{date}', 'CounterController@set');
    Route::get('/counter/finance/download/excel/{key}', 'CounterController@getExcel');
    Route::get('/counter/biz/download/excel', 'CounterController@bizExcel');

    Route::get('/counter/biz', 'CounterController@biz');

});


Route::get('/test', function() {
    $a = new App\Helpers\Date;
    $a->dateRange('today');
});








