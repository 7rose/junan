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

Route::get('/', function () {
    $a = new App\Helpers\Date;
    $b =  $a->ageFromId('320823197912187037');
    // $b =  $a->birthdayFromId('320823197912187037');
    $c =  $a->availableId('320823196912187037', '20-50');
    echo $b;
    // echo carbon->today();
});

Route::get('/insert', function () {
    return view('users/insert');
});

Route::get('/customer/create', 'CustomerController@create');
Route::get('/customer/{id}', 'CustomerController@show');
Route::post('/customer/store', ['as'=>'customer.store', 'uses'=>'CustomerController@store']);

    // Route::get('/staff/auto_login', 'StaffController@autoLogin');
    // Route::get('/staffing', 'StaffController@index');
    // Route::get('/staff/create', 'StaffController@create');
    // Route::post('/staff/store', ['as'=>'staff.store', 'uses'=>'StaffController@store']);
    // Route::get('/staff/show/{id?}', 'StaffController@show');
    // Route::get('/staff/edit/{id?}', 'StaffController@edit');
    // Route::post('/staff/update/{id?}', 'StaffController@update');
    // Route::get('/staff/lock/{id?}', 'StaffController@lock');
    // Route::get('/staff/unlock/{id?}', 'StaffController@unlock');
    // Route::get('/staff/delete/{id?}', 'StaffController@delete');
    // Route::post('/staff/image/{id?}', 'StaffController@setImage');
