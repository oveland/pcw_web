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
    Route::prefix(__('reports'))->group(function () {
        Route::prefix(__('geolocation'))->group(function () {
            Route::get('/address/{offRoad}', 'GeolocationController@getAddressFromCoordinates')->name('geolocation-address');
            Route::get('/image/{offRoad}', 'GeolocationController@getImageFromCoordinate')->name('geolocation-image');
        });

        /* General reports */
        Route::prefix(__('routes'))->group(function () {
            /* Route report */
            Route::prefix(__('route-report'))->group(function () {
                Route::get('/', 'RouteReportController@index')->name('route-report');
                Route::get('/show', 'RouteReportController@show')->name('route-search-report');
                Route::any('/chart/{dispatchRegister}', 'RouteReportController@chart')->name('route-chart-report');
                Route::any('/off_road/{dispatchRegister}', 'RouteReportController@offRoadReport')->name('route-off-road-report');
                Route::any('/ajax', 'RouteReportController@ajax')->name('route-ajax-action');
            });

            /* Off Road report */
            Route::prefix(__('off-road'))->group(function () {
                Route::get('/', 'OffRoadController@index')->name('off-road-report');
                Route::get('/show', 'OffRoadController@searchReport')->name('off-road-search-report');
                Route::any('/ajax', 'OffRoadController@ajax')->name('off-road-ajax-action');
            });
        });

        /* Routes for passenger report */
        Route::prefix(__('passengers'))->group(function () {
            /* Router for Tax Central Reports */
            Route::prefix('taxcentral')->group(function () {
                Route::get('/', 'TaxCentralPassengerReportController@index')->name('tc-passengers-report');
                Route::get('/show', 'TaxCentralPassengerReportController@show')->name('tc-passengers-search-report');
                Route::any('/dispatch/show/{dispatchRegister}', 'TaxCentralPassengerReportController@showByDispatch')->name('tc-passengers-by-dispatch');
                Route::any('/seat/show/{historySeat}', 'TaxCentralPassengerReportController@showHistorySeat')->name('tc-passengers-seat-detail');
                Route::any('/ajax/{action}', 'TaxCentralPassengerReportController@ajax')->name('tc-passengers-ajax');
            });
            /* Router for General Reports */
            Route::prefix(__('consolidated'))->group(function () {
                Route::prefix(__('daily'))->group(function () {
                    Route::get('/','PassengerReportController@index')->name('passengers-report-consolidated-days');
                    Route::get('/show', 'PassengerReportController@show')->name('passengers-search-report-days');
                    Route::get('/export', 'PassengerReportController@export')->name('passengers-export-report-days');
                    Route::any('/ajax/{action}', 'PassengerReportController@ajax')->name('passengers-ajax');
                });

                Route::prefix(__('date-range'))->group(function () {
                    Route::get('/','PassengersReportDateRangeController@index')->name('passengers-report-consolidated-range');
                    Route::get('/show', 'PassengersReportDateRangeController@show')->name('passengers-search-report-range');
                    Route::get('/export', 'PassengersReportDateRangeController@export')->name('passengers-export-report-range');
                    Route::any('/ajax/{action}', 'PassengerReportController@ajax')->name('passengers-ajax');
                });
            });
        });

        /* Access log report */
        Route::prefix(__('users'))->group(function () {
            Route::prefix(__('access-log'))->group(function () {
                /* Routes for logs */
                Route::get('/', 'AccessLogController@index')->name('logs-access');
                Route::get('/{date}', 'AccessLogController@report')->name('logs-access-export');
            });
        });
    });

    /****************** MIGRATION ROUTES *******************/

    /* Routes for migrate Tables */
    Route::prefix(__('migrate'))->group(function () {
        Route::get('/', 'MigrationController@index')->name('migrate');
        Route::get('/companies', 'MigrationController@migrateCompanies')->name('migrate-companies');
        Route::get('/routes', 'MigrationController@migrateRoutes')->name('migrate-routes');
        Route::get('/users', 'MigrationController@migrateUsers')->name('migrate-users');
        Route::get('/vehicles', 'MigrationController@migrateVehicles')->name('migrate-vehicles');
        Route::get('/control-points', 'MigrationController@migrateControlPoints')->name('migrate-control-points');
        Route::get('/control-point-time', 'MigrationController@migrateControlPointTimes')->name('migrate-control-point-times');
        Route::get('/fringes', 'MigrationController@migrateFringes')->name('migrate-fringes');
        Route::get('/coordinates/{route}', 'MigrationControlPointController@exportCoordinates')->name('export-coordinates');
        /* Routes for migrate Control Points (CP) */
        Route::prefix(__('cp'))->group(function () {
            Route::get('/', 'MigrationControlPointController@getControlPoints')->name('migrate-cp');
            Route::get('/compare/{route}', 'MigrationControlPointController@compare')->name('compare-control-point');
        });
    });

    /* Routes for tools */
    Route::get('/tools/map', 'ToolsController@map')->name('map-tool');
});