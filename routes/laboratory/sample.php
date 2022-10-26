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
Route::get('sample_services'           , 'General\SampleController@index');
Route::post('sample_services'          , 'General\SampleController@create');
Route::get('sample_services/{id}'      , 'General\SampleController@show');
Route::get('sample_services/{id}/edit' , 'General\SampleController@edit');
Route::put('sample_services/{id}'      , 'General\SampleController@update');
Route::delete('sample_services/{id}'   , 'General\SampleController@destroy');