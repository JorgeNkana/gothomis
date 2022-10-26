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
Route::get('lab_results'           , 'General\ResultController@index');
Route::post('lab_results'          , 'General\ResultController@create');
Route::get('lab_results/{id}'      , 'General\ResultController@show');
Route::get('lab_results/{id}/edit' , 'General\ResultController@edit');
Route::put('lab_results/{id}'      , 'General\ResultController@update');
Route::delete('lab_results/{id}'   , 'General\ResultController@destroy');