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

Auth::routes();

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
    Route::any('/report/off_road/{dispatchRegister}', 'RouteReportController@offRoadReport')->name('route-off-road-report');
    Route::any('/report/ajax', 'RouteReportController@ajax')->name('route-ajax-action');

    /* Routes for passenger report */
    Route::get('/pasajeros', 'PassengerReportController@index')->name('passengers-report');
    Route::get('/passengers/show', 'PassengerReportController@show')->name('passengers-search-report');
    Route::any('/passengers/dispatch/show/{dispatchRegister}', 'PassengerReportController@showByDispatch')->name('passengers-by-dispatch');
    Route::any('/passengers/seat/show/{historySeat}', 'PassengerReportController@showHistorySeat')->name('passengers-seat-detail');
    Route::any('/passengers/ajax/{action}', 'PassengerReportController@ajax')->name('passengers-ajax');



    /****************** MIGRATION ROUTES *******************/

    /* Routes for migrate Tables */
    Route::get('/migrate/', 'MigrationController@index')->name('migrate');
    Route::get('/migrate/companies', 'MigrationController@migrateCompanies')->name('migrate-companies');
    Route::get('/migrate/routes', 'MigrationController@migrateRoutes')->name('migrate-routes');
    Route::get('/migrate/users', 'MigrationController@migrateUsers')->name('migrate-users');
    Route::get('/migrate/vehicles', 'MigrationController@migrateVehicles')->name('migrate-vehicles');
    Route::get('/migrate/control-points', 'MigrationController@migrateControlPoints')->name('migrate-control-points');
    Route::get('/migrate/control-point-time', 'MigrationController@migrateControlPointTimes')->name('migrate-control-point-times');

    /* Routes for migrate Control Points (CP) */
    Route::get('/migrate/cp/','MigrationControlPointController@getControlPoints')->name('migrate-cp');
    Route::get('/migrate/cp/compare/{route}', 'MigrationControlPointController@compare')->name('compare-control-point');
    Route::get('/migrate/coordinates/{route}', 'MigrationControlPointController@exportCoordinates')->name('export-coordinates');

    /* Routes for tools */
    Route::get('/tools/map', 'ToolsController@map')->name('map-tool');

    /* Routes for logs */
    Route::get('logs/access','AccessLogController@index')->name('logs-access');
    Route::get('logs/access/{date}','AccessLogController@report')->name('logs-access-export');
});