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

    // 学员
    Route::get('/customer', 'CustomerController@index');
    Route::get('/customer/create', 'CustomerController@create');
    Route::get('/customer/{id}', 'CustomerController@show');
    Route::post('/customer/store', ['as'=>'customer.store', 'uses'=>'CustomerController@store']);
    Route::post('/customer/seek', ['as'=>'customer.seek', 'uses'=>'CustomerController@seek']);
    Route::get('/customer/seek/reset', 'CustomerController@seekReset');
    Route::get('/customer/edit/{id}', 'CustomerController@edit');
    Route::post('/customer/update/{id}', ['as'=>'customer.update', 'uses'=>'CustomerController@update']);

    // 员工
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

    // 业务
    Route::get('/customer/biz/{id}', 'BizController@create');
    Route::post('/customer/biz/store', ['as'=>'biz.store', 'uses'=>'BizController@store']);

    // 财务
    Route::get('/finance', 'FinanceController@index');
    Route::post('/finance/seek', ['as'=>'finance.seek', 'uses'=>'FinanceController@seek']);
    Route::get('/finance/seek/reset', 'FinanceController@seekReset');
    Route::get('/finance/create/{id}', 'FinanceController@create');
    Route::post('/finance/store', ['as'=>'finance.store', 'uses'=>'FinanceController@store']);

});


Route::get('/test', function() {
    echo  Request::path();
});








