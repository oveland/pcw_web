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

Route::get('/', function(){
    return redirect(route('route-report'));
})->name('home');

/* Routes for route report */
Route::get('/ruta', 'ReportController@index')->name('route-report');
Route::get('/report/show/', 'ReportController@show')->name('route-search-report');
Route::any('/report/chart/{dispatchRegister}', 'ReportController@chart')->name('route-chart-report');
Route::any('/report/ajax/', 'ReportController@ajax')->name('route-ajax-action');

/* Routes for route report */
Route::get('/pasajeros', 'ReportPassengerController@index')->name('passengers-report');
Route::get('/passengers/show/', 'ReportPassengerController@show')->name('passengers-search-report');