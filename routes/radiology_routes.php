<?php

Route::get('clients-radiology'           , 'Radiology\ClientRadiologyController@index');
Route::post('clients-radiology'          , 'Radiology\ClientRadiologyController@create');
Route::get('clients-radiology/{id}'      , 'Radiology\ClientRadiologyController@show');
Route::get('clients-radiology/{id}/edit' , 'Radiology\ClientRadiologyController@edit');
Route::put('clients-radiology/{id}'      , 'Radiology\ClientRadiologyController@update');
Route::delete('clients-radiology/{id}'   , 'Radiology\ClientRadiologyController@destroy');