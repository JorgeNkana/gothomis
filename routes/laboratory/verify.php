<?php

/*
|--------------------------------------------------------------------------
| Equipment Configuration Routes by Japhari Mbaru
|--------------------------------------------------------------------------
|
| Here is where you can equipment configuration routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::get('verify_services'           , 'General\VerifyController@index');
Route::post('verify_services'          , 'General\VerifyController@create');
Route::get('verify_services/{id}'      , 'General\VerifyController@show');
Route::get('verify_services/{id}/edit' , 'General\VerifyController@edit');
Route::put('verify_services/{id}'      , 'General\VerifyController@update');
Route::delete('verify_services/{id}'   , 'General\VerifyController@destroy');