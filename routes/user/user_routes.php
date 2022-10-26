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
Route::get('users', 'User\UserController@index');
Route::post('users', 'User\UserController@create');
Route::get('users/{id}', 'User\UserController@show');
Route::get('users/{id}/edit', 'User\UserController@edit');
Route::put('users/{id}', 'User\UserController@update');
Route::delete('users/{id}', 'User\UserController@destroy');