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
Route::post('investigation-lists', 'General\GeneralLaboratory@investigationList');
Route::get('investigation-verify', 'General\GeneralLaboratory@investigationVerify');
Route::post('doctor-performance', 'General\GeneralLaboratory@doctorPerformance');
Route::post('order-lists', 'General\GeneralLaboratory@orderLists');
Route::post('test-per-patient', 'General\GeneralLaboratory@orderPerPatient');
Route::post('labpatients-lists', 'General\GeneralLaboratory@labPatientsLists');
Route::post('get-investigation-lists', 'General\GeneralLaboratory@investigationPatientsLists');
Route::get('investigation-test-list', 'General\GeneralLaboratory@testList');
Route::post('region-id', 'Region\RegionController@regionId');
Route::post('council-id', 'Region\RegionController@councilId');