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
        return redirect(route('report-route'));
    })->name('home');

    Route::get('/home', function(){
        return redirect(route('report-route'));
    })->name('index');

    /* Routes for general actions */
    Route::prefix(__('general'))->group(function () {
        Route::any('/load-select-routes', 'GeneralController@loadSelectRoutes')->name('general-load-select-routes');
        Route::any('/load-select-route-round-trips', 'GeneralController@loadSelectRouteRoundTrips')->name('general-load-select-route-round-trips');
        Route::any('/load-select-vehicles', 'GeneralController@loadSelectVehicles')->name('general-load-select-vehicles');
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

            Route::prefix(__('maintenance'))->group(function () {
                Route::get('/', 'MaintenanceVehicleController@index')->name('admin-vehicles-maintenance');
                Route::get('/show', 'MaintenanceVehicleController@show')->name('admin-vehicles-maintenance-show');
                Route::post('/create/{vehicle}', 'MaintenanceVehicleController@create')->name('admin-vehicles-maintenance-create');
                Route::put('/update/{maintenanceVehicle}', 'MaintenanceVehicleController@update')->name('admin-vehicles-maintenance-update');
                Route::delete('/delete/{company}', 'MaintenanceVehicleController@delete')->name('admin-vehicles-maintenance-delete');
            });
        });

        Route::prefix(__('gps'))->group(function () {
            Route::prefix(__('url-manage'))->group(function () {
                Route::get('/', 'ManagerGPSController@index')->name('admin-gps-manage');
                Route::get('/list', 'ManagerGPSController@list')->name('admin-gps-manage-list');
                Route::get('/get-vehicle-status', 'ManagerGPSController@getVehicleStatus')->name('admin-gps-get-vehicle-status');
                Route::post('/send-sms', 'ManagerGPSController@sendSMS')->name('admin-gps-manage-send-sms');
                Route::post('/update-sim-gps/{simGPS}', 'ManagerGPSController@updateSIMGPS')->name('admin-gps-manage-update-sim-gps');
                Route::post('/create-sim-gps', 'ManagerGPSController@createSIMGPS')->name('admin-gps-manage-create-sim-gps');
                Route::delete('/delete-sim-gps/{simGPS}', 'ManagerGPSController@deleteSIMGPS')->name('admin-gps-manage-delete-sim-gps');
                Route::get('/get-script/{device}', 'ManagerGPSController@getScript')->name('admin-gps-manage-get-script');
            });
        });

        Route::prefix(__('drivers'))->group(function () {
            Route::prefix(__('excel'))->group(function () {
                Route::get('/', 'DriverController@index')->name('admin-drivers');
                Route::post('/csv', 'DriverController@csv')->name('admin-drivers-csv');
            });
        });
    });

    /* Routes for route report */
    Route::prefix(__('reports'))->group(function () {
        /* General reports */
        Route::prefix(__('routes'))->group(function () {
            /* Route report */
            Route::prefix(__('route-report'))->group(function () {
                Route::get('/', 'RouteReportController@index')->name('report-route');
                Route::get('/show', 'RouteReportController@show')->name('report-route-search');
                Route::any('/chart/{dispatchRegister}', 'RouteReportController@chart')->name('report-route-chart');
                Route::any('/off_road/{dispatchRegister}', 'RouteReportController@offRoadReport')->name('report-route-off-road');
                Route::any('/ajax', 'RouteReportController@ajax')->name('route-ajax-action');
            });

            /* Off Road report */
            Route::prefix(__('off-road'))->group(function () {
                Route::get('/', 'OffRoadController@index')->name('report-route-off-road-index');
                Route::get('/show', 'OffRoadController@searchReport')->name('report-route-off-road-search');
                Route::get('/address/{offRoad}', 'OffRoadController@getAddressFromCoordinates')->name('report-route-off-road-geolocation-address');
                Route::get('/image/{offRoad}', 'OffRoadController@getImageFromCoordinate')->name('report-route-off-road-geolocation-image');
                Route::any('/ajax', 'OffRoadController@ajax')->name('report-route-off-road-ajax-action');
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
            Route::prefix(__('speeding'))->group(function () {
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
                Route::get('/', 'TaxCentralPassengerReportController@index')->name('report-passengers-taxcentral-report');
                Route::get('/show', 'TaxCentralPassengerReportController@show')->name('report-passengers-taxcentral-search-report');
                Route::any('/dispatch/show/{dispatchRegister}', 'TaxCentralPassengerReportController@showByDispatch')->name('report-passengers-taxcentral-by-dispatch');
                Route::any('/seat/show/{historySeat}', 'TaxCentralPassengerReportController@showHistorySeat')->name('report-passengers-taxcentral-seat-detail');
                Route::any('/ajax/{action}', 'TaxCentralPassengerReportController@ajax')->name('report-passengers-taxcentral-ajax');
            });

            /* Router for recorders reports */
            Route::prefix(__('recorders'))->group(function () {
                /* Router for General Reports */
                Route::prefix(__('consolidated'))->group(function () {
                    Route::prefix(__('daily'))->group(function () {
                        Route::get('/','PassengerReportController@index')->name('report-passengers-recorders-consolidated-daily');
                        Route::get('/show', 'PassengerReportController@show')->name('report-passengers-recorders-consolidated-daily-search');
                        Route::get('/export', 'PassengerReportController@export')->name('report-passengers-recorders-consolidated-daily-export');
                        Route::any('/ajax/{action}', 'PassengerReportController@ajax')->name('report-passengers-recorders-consolidated-daily-ajax-action');
                    });

                    Route::prefix(__('date-range'))->group(function () {
                        Route::get('/','PassengersReportDateRangeController@index')->name('report-passengers-recorders-consolidated-date-range');
                        Route::get('/show', 'PassengersReportDateRangeController@show')->name('report-passengers-recorders-consolidated-date-range-search');
                        Route::get('/export', 'PassengersReportDateRangeController@export')->name('report-passengers-recorders-consolidated-date-range-export');
                    });
                });

                /* Router for General Reports */
                Route::prefix(__('detailed'))->group(function () {
                    Route::prefix(__('daily'))->group(function () {
                        Route::get('/','PassengerReportDetailedController@index')->name('report-passengers-recorders-detailed-daily');
                        Route::get('/show', 'PassengerReportDetailedController@show')->name('report-passengers-recorders-detailed-daily-search');
                        Route::get('/export', 'PassengerReportDetailedController@export')->name('report-passengers-recorders-detailed-daily-export');
                    });

                    Route::prefix(__('date-range'))->group(function () {
                        Route::get('/','PassengerReportDetailedDateRangeController@index')->name('report-passengers-recorders-detailed-date-range');
                        Route::get('/show', 'PassengerReportDetailedDateRangeController@show')->name('report-passengers-recorders-detailed-date-range-search');
                        Route::get('/export', 'PassengerReportDetailedDateRangeController@export')->name('report-passengers-recorders-detailed-date-range-export');
                    });
                });

                Route::prefix(__('fringes'))->group(function () {
                    Route::get('/', 'RecorderPassengerReportByFringesController@index')->name('report-passengers-recorders-fringes');
                    Route::get('/search', 'RecorderPassengerReportByFringesController@search')->name('report-passengers-recorders-fringes-search');
                });
            });

            Route::prefix(__('sensors'))->group(function () {
                Route::prefix(__('counter'))->group(function () {
                    Route::get('/', 'PassengerReportCounterController@index')->name('report-passengers-sensors-counter');
                    Route::get('/list', 'PassengerReportCounterController@list')->name('report-passengers-sensors-counter-list');
                    Route::get('/show-counter-issue/{counterIssue}', 'PassengerReportCounterController@showCounterIssue')->name('report-passengers-sensors-counter-issue');
                });

                Route::prefix(__('seats'))->group(function () {
                    Route::get('/', 'SeatReportController@index')->name('report-passengers-sensors-seats');
                    Route::get('/play', 'SeatReportController@play')->name('report-passengers-sensors-seats-play');
                });
            });
        });

        /* Routes for drivers report */
        Route::prefix(__('drivers'))->group(function () {
            Route::prefix(__('consolidated'))->group(function () {
                Route::get('/','DriverConsolidatedController@index')->name('report-drivers-consolidated');
                Route::get('/show', 'DriverConsolidatedController@show')->name('report-drivers-consolidated-search');
                Route::get('/export', 'DriverConsolidatedController@export')->name('report-drivers-consolidated-export');
            });

            /* Router for General Reports */
            Route::prefix(__('detailed'))->group(function () {
                Route::get('/','DriverDetailedController@index')->name('report-drivers-detailed');
                Route::get('/show', 'DriverDetailedController@show')->name('report-drivers-detailed-search');
                Route::get('/export', 'DriverDetailedController@export')->name('report-drivers-detailed-export');
            });
        });

        /* Access log report */
        Route::prefix(__('users'))->group(function () {
            Route::prefix(__('access-log'))->group(function () {
                /* Routes for logs */
                Route::get('/', 'AccessLogController@index')->name('report-user-access-log');
                Route::get('/{date}', 'AccessLogController@report')->name('report-user-access-log-export');
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
            Route::get('/compare/{route}', 'MigrationControlPointController@compare')->name('migrate-cp-compare');
        });
    });

    /* Routes for tools */
    Route::prefix(__('tools'))->group(function () {
        Route::prefix(__('map'))->group(function () {
            Route::get('/', 'ToolsController@map')->name('tools-map');
            Route::get('/get-route-distance/{route}', 'ToolsController@getRouteDistance')->name('tools-map-get-route-distance');
            Route::get('/get-route-distance', 'ToolsController@getRouteDistanceFromUrl')->name('tools-map-get-route-distance-from-url');
        });
    });
});

Route::prefix('api')->group(function () {
    Route::prefix('peak-and-plate')->group(function () {
        Route::get('/{company}', 'ApiPeakAndPlateController@getVehiclesCurrentPeakAndPlate')->name('api-peak-and-plate-get-vehicles-current-peak-and-plate');
    });
});
