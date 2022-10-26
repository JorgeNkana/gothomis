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
Route::get('client_tribe'           , 'Tribe\ClientTribeController@index');
Route::post('client_tribe'          , 'Tribe\ClientTribeController@create');
Route::get('client_tribe/{id}'      , 'Tribe\ClientTribeController@show');
Route::get('client_tribe/{id}/edit' , 'Tribe\ClientTribeController@edit');
Route::put('client_tribe/{id}'      , 'Tribe\ClientTribeController@update');
Route::delete('client_tribe/{id}'   , 'Tribe\ClientTribeController@destroy');