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

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function(){
        return redirect(route('route-report'));
    })->name('home');

    Route::get('/home', function(){
        return redirect(route('route-report'));
    })->name('index');

    /* Routes for route report */
    Route::get('/ruta', 'RouteReportController@index')->name('route-report');
    Route::get('/report/show', 'RouteReportController@show')->name('route-search-report');
    Route::any('/report/chart/{dispatchRegister}', 'RouteReportController@chart')->name('route-chart-report');
    Route::any('/report/ajax', 'RouteReportController@ajax')->name('route-ajax-action');

    /* Routes for passenger report */
    Route::get('/pasajeros', 'PassengerReportController@index')->name('passengers-report');
    Route::get('/passengers/show', 'PassengerReportController@show')->name('passengers-search-report');
    Route::any('/passengers/dispatch/show/{dispatchRegister}', 'PassengerReportController@showByDispatch')->name('passengers-by-dispatch');
    Route::any('/passengers/ajax', 'PassengerReportController@ajax')->name('passengers-ajax-action');

    Route::get('/migrate/', 'MigrationController@index')->name('migrate');
    Route::get('/migrate/companies', 'MigrationController@migrateCompanies')->name('migrate-companies');
    Route::get('/migrate/routes', 'MigrationController@migrateRoutes')->name('migrate-routes');
    Route::get('/migrate/users', 'MigrationController@migrateUsers')->name('migrate-users');
    Route::get('/migrate/vehicles', 'MigrationController@migrateVehicles')->name('migrate-vehicles');
    Route::get('/migrate/control-points', 'MigrationController@migrateControlPoints')->name('migrate-control-points');
});

Auth::routes();