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
Route::get('client_registration'           , 'Client\ClientRegistrationController@index');
Route::post('client_registration'          , 'Client\ClientRegistrationController@client_registration');
Route::get('client_registration/{id}'      , 'Client\ClientRegistrationController@show');
Route::get('client_registration/{id}/edit' , 'Client\ClientRegistrationController@edit');
Route::put('client_registration/{id}'      , 'Client\ClientRegistrationController@update');
Route::delete('client_registration/{id}'   , 'Client\ClientRegistrationController@destroy');