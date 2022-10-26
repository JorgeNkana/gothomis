<?php

/*
|--------------------------------------------------------------------------
| Radiology Investigation Routes by Japhari Mbaru
|--------------------------------------------------------------------------
|
| Here is where you can register radiology routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::post('search-tribes', 'Tribe\ClientTribeController@searchTribes');