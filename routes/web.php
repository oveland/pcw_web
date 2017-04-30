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

Route::get('/', 'ReportController@index');
Route::get('/show/', 'ReportController@show')->name('search-report');
Route::any('/ajax/', 'ReportController@ajax')->name('ajax-action');
