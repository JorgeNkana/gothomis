<?php
Route::get('item_prices'           , 'Items\PriceController@index');
Route::post('item_prices'          , 'Items\PriceController@create');
Route::get('item_prices/{id}'      , 'Items\PriceController@show');
Route::get('item_prices/{id}/edit' , 'Items\PriceController@edit');
Route::put('item_prices/{id}'      , 'Items\PriceController@update');
Route::delete('item_prices/{id}'   , 'Items\PriceController@destroy');