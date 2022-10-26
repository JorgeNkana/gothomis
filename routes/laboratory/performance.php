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
Route::get('doctor_performance'           , 'General\DoctorPerformanceController@index');
Route::post('doctor_performance'          , 'General\DoctorPerformanceController@create');
Route::get('doctor_performance/{id}'      , 'General\DoctorPerformanceController@show');
Route::get('doctor_performance/{id}/edit' , 'General\DoctorPerformanceController@edit');
Route::put('doctor_performance/{id}'      , 'General\DoctorPerformanceController@update');
Route::delete('doctor_performance/{id}'   , 'General\DoctorPerformanceController@destroy');