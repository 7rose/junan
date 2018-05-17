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

    // 员工
    Route::get('/user', 'UserController@index');
    Route::get('/user/create', 'UserController@create');
    Route::post('/user/store', ['as'=>'user.store', 'uses'=>'UserController@store']);
    Route::get('/user/{id}', 'UserController@show');
    Route::post('/user/seek', ['as'=>'user.seek', 'uses'=>'UserController@seek']);
    Route::get('/user/seek/reset', 'UserController@seekReset');
    Route::get('/user/lock/{id}', 'UserController@lock');
    Route::get('/user/unlock/{id}', 'UserController@unlock');

    // 业务
    Route::get('/customer/biz/{id}', 'BizController@create');
    Route::post('/customer/biz/store', ['as'=>'biz.store', 'uses'=>'BizController@store']);

});

