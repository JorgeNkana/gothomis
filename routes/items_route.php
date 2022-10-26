<?php
Route::get('items'              , 'Items\ItemController@index');
Route::post('items'             , 'Items\ItemController@create');
Route::post('items/disable'     , 'Items\ItemController@disableNow');
Route::post('items/reDisable'   , 'Items\ItemController@reDisableNow');
Route::get('items/{id}'         , 'Items\ItemController@show');
Route::get('items_departments'  , 'Items\ItemController@getAllDepartments');
Route::get('items_disabled'     , 'Items\ItemController@disabled');
Route::get('items/{id}/edit'    , 'Items\ItemController@edit');
Route::put('items/{id}'         , 'Items\ItemController@update');
Route::delete('items/{id}'      , 'Items\ItemController@destroy');