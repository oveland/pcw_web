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
                Route::get('/address/{offRoad}', 'OffRoadController@getAddressFromCoordinates')->name('off-road-geolocation-address');
                Route::get('/image/{offRoad}', 'OffRoadController@getImageFromCoordinate')->name('off-road-geolocation-image');
                Route::any('/ajax', 'OffRoadController@ajax')->name('off-road-ajax-action');
            });

            /* Control Points report */
            Route::prefix(__('control-points'))->group(function () {
                Route::get('/', 'ControlPointsReportController@index')->name('report-route-control-points');
                Route::get('/show', 'ControlPointsReportController@searchReport')->name('report-route-control-points-search-report');
                Route::get('/export', 'ControlPointsReportController@export')->name('report-route-control-points-export-report');
                Route::any('/ajax', 'ControlPointsReportController@ajax')->name('report-route-control-points-ajax-action');
            });
        });

        Route::prefix(__('url-vehicles'))->group(function(){
            /* Off Road report */
            Route::prefix(__('parked'))->group(function () {
                Route::get('/', 'ParkedVehiclesReportController@index')->name('report-vehicle-parked');
                Route::get('/show', 'ParkedVehiclesReportController@searchReport')->name('report-vehicle-parked-search-report');
                Route::get('/address/{parkingReport}', 'ParkedVehiclesReportController@getAddressFromCoordinates')->name('report-vehicle-parked-geolocation-address');
                Route::get('/image/{parkingReport}', 'ParkedVehiclesReportController@getImageFromCoordinate')->name('report-vehicle-parked-geolocation-image');
                Route::any('/ajax', 'ParkedVehiclesReportController@ajax')->name('report-vehicle-parked-ajax-action');
            });

            /* Speeding report */
            Route::prefix(__('speeding-vehicle'))->group(function () {
                Route::get('/', 'SpeedingController@index')->name('report-vehicle-speeding');
                Route::get('/show', 'SpeedingController@searchReport')->name('report-vehicle-speeding-search-report');
                Route::get('/address/{speeding}', 'SpeedingController@getAddressFromCoordinates')->name('report-vehicle-speeding-geolocation-address');
                Route::get('/image/{speeding}', 'SpeedingController@getImageLocationFromCoordinates')->name('report-vehicle-speeding-geolocation-image');
                Route::any('/ajax', 'SpeedingController@ajax')->name('report-vehicle-speeding-ajax-action');
            });

            /* Speeding report */
            Route::prefix(__('status'))->group(function () {
                Route::get('/', 'VehicleStatusReportController@index')->name('report-vehicle-status');
                Route::get('/show', 'VehicleStatusReportController@searchReport')->name('report-vehicle-status-search-report');
            });
        });

        /* Routes for passenger report */
        Route::prefix(__('passengers'))->group(function () {
            /*Edit reports*/
            Route::prefix('manage')->group(function () {
                Route::any('/ajax/{action}', 'ManagePassengersByRecorderController@ajax')->name('report-passengers-manage-update');
            });

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
                    Route::get('/','PassengerReportController@index')->name('passengers-consolidated-report-days');
                    Route::get('/show', 'PassengerReportController@show')->name('passengers-consolidated-search-report-days');
                    Route::get('/export', 'PassengerReportController@export')->name('passengers-consolidated-export-report-days');
                    Route::any('/ajax/{action}', 'PassengerReportController@ajax')->name('passengers-ajax');
                });

                Route::prefix(__('date-range'))->group(function () {
                    Route::get('/','PassengersReportDateRangeController@index')->name('passengers-consolidated-report-range');
                    Route::get('/show', 'PassengersReportDateRangeController@show')->name('passengers-consolidated-search-report-range');
                    Route::get('/export', 'PassengersReportDateRangeController@export')->name('passengers-consolidated-export-report-range');
                });
            });

            /* Router for General Reports */
            Route::prefix(__('detailed'))->group(function () {
                Route::prefix(__('daily'))->group(function () {
                    Route::get('/','PassengerReportDetailedController@index')->name('passengers-detailed-report-days');
                    Route::get('/show', 'PassengerReportDetailedController@show')->name('passengers-detailed-search-days');
                    Route::get('/export', 'PassengerReportDetailedController@export')->name('passengers-detailed-export-days');
                });

                Route::prefix(__('date-range'))->group(function () {
                    Route::get('/','PassengerReportDetailedDateRangeController@index')->name('passengers-detailed-report-range');
                    Route::get('/show', 'PassengerReportDetailedDateRangeController@show')->name('passengers-detailed-search-report-range');
                    Route::get('/export', 'PassengerReportDetailedDateRangeController@export')->name('passengers-detailed-export-report-range');
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

    /* Routes for admin pages */
    Route::prefix(__('url-administration'))->group(function () {
        Route::prefix(__('url-vehicles'))->group(function () {
            Route::prefix(__('peak-and-plate'))->group(function () {
                Route::get('/', 'PeakAndPlateController@index')->name('admin-vehicles-peak-and-plate');
                Route::get('/show', 'PeakAndPlateController@show')->name('admin-vehicles-peak-and-plate-show');
                Route::post('/update', 'PeakAndPlateController@update')->name('admin-vehicles-peak-and-plate-update');
                Route::post('/reset', 'PeakAndPlateController@reset')->name('admin-vehicles-peak-and-plate-reset');
            });
        });

        Route::prefix(__('gps'))->group(function () {
            Route::prefix(__('url-manage'))->group(function () {
                Route::get('/', 'ManagerGPSController@index')->name('admin-gps-manage');
                Route::get('/list', 'ManagerGPSController@list')->name('admin-gps-manage-list');
                Route::get('/get-vehicle-status', 'ManagerGPSController@getVehicleStatus')->name('admin-gps-get-vehicle-status');
                Route::post('/send-sms', 'ManagerGPSController@sendSMS')->name('admin-gps-manage-send-sms');
                Route::post('/update-sim-gps/{simGPS}', 'ManagerGPSController@updateSIMGPS')->name('admin-gps-manage-update-sim-gps');
            });
        });

        Route::prefix(__('counter'))->group(function () {
            Route::prefix(__('report'))->group(function () {
                Route::get('/', 'StatusCounterController@index')->name('admin-counter-status');
                Route::get('/list', 'StatusCounterController@list')->name('admin-counter-status-list');
                Route::get('/show-counter-issue/{counterIssue}', 'StatusCounterController@showCounterIssue')->name('admin-counter-status-show-counter-issue');
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
    Route::get('/tools/get-route-distance/{route}', 'ToolsController@getRouteDistance')->name('tools-map-get-route-distance');
    Route::get('/tools/get-route-distance', 'ToolsController@getRouteDistanceFromUrl')->name('tools-map-get-route-distance-from-url');
});

Route::prefix('api')->group(function () {
    Route::prefix('peak-and-plate')->group(function () {
        Route::get('/{company}', 'ApiPeakAndPlateController@getVehiclesCurrentPeakAndPlate')->name('api-peak-and-plate-get-vehicles-current-peak-and-plate');
    });
});
