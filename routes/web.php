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

use App\Models\Users\User;

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return redirect(route('report-route'));
    })->name('home');

    Route::get('/home', function () {
        return redirect(route('report-route'));
    })->name('index');


    /* Example routes for vue development */
    Route::prefix(__('example'))->group(function () {
        Route::get('/', 'Example\ExampleController@index')->name('example');

        Route::prefix(__('search'))->group(function () {
            Route::get(__('/'), 'Example\ExampleController@search')->name('example.search');
        });

        Route::prefix(__('url-params'))->group(function () {
            Route::get('/{name}', 'Example\ExampleController@getParams')->name('example.params.get');
            Route::any('/{name}/'.__('save'), 'Example\ExampleController@setParams')->name('example.params.set');
        });
    });

    /* Routes for general actions */
    Route::prefix(__('general'))->group(function () {
        Route::any('/load-select-routes', 'GeneralController@loadSelectRoutes')->name('general-load-select-routes');
        Route::any('/load-select-drivers', 'GeneralController@loadSelectDrivers')->name('general-load-select-drivers');
        Route::any('/load-select-route-round-trips', 'GeneralController@loadSelectRouteRoundTrips')->name('general-load-select-route-round-trips');
        Route::any('/load-select-vehicles-from-route', 'GeneralController@loadSelectVehiclesFromRoute')->name('general-load-select-vehicles-from-route');
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

        Route::prefix(__('proprietaries'))->group(function () {
            Route::get('/', 'ProprietaryController@index')->name('admin-proprietaries-manage');
            Route::get('/show', 'ProprietaryController@show')->name('admin-proprietaries-show');
        });

        /* Example routes for vue development */
        Route::prefix(__('rocket'))->group(function () {
            Route::get('/', 'Rocket\RocketController@index')->name('admin.rocket');

            Route::prefix(__('search'))->group(function () {
                Route::get(__('/'), 'Rocket\RocketController@search')->name('admin.rocket.search');
            });

            Route::prefix(__('url-params'))->group(function () {
                Route::get('/{name}', 'Rocket\RocketController@getParams')->name('admin.rocket.params.get');
                Route::any('/{name}/'.__('save'), 'Rocket\RocketController@setParams')->name('admin.rocket.params.set');
            });
        });
    });

    /* Routes for operation pages */
    Route::prefix(__('url-operation'))->group(function () {
        Route::prefix(__('dispatch'))->group(function () {
            Route::prefix(__('url-auto-dispatcher'))->group(function () {
                Route::get('/', 'AutoDispatcherController@index')->name('operation-dispatch-auto-dispatcher');
                Route::get('/show', 'AutoDispatcherController@show')->name('operation-dispatch-auto-dispatcher-show');
                Route::post('/reassign-route', 'AutoDispatcherController@reassignRoute')->name('operation-dispatch-auto-dispatcher-reassign-route');
            });
        });

        Route::prefix(__('track'))->group(function () {
            Route::prefix(__('map'))->group(function () {
                Route::get('/', 'OperationTrackMapController@index')->name('operation-track-map');
                Route::get('/get', 'OperationTrackMapController@get')->name('operation-track-map-get');
            });
        });
    });

    /* Routes for route report */
    Route::prefix(__('reports'))->group(function () {
        /* General reports */

        /* Apps reports */
        Route::prefix(__('app'))->group(function () {
            Route::get('/', 'Apps\AppsReportController@index')->name('report.app');

            Route::prefix(__('search'))->group(function () {
                Route::get(__('/'), 'Apps\AppsReportController@search')->name('report.app.search');
            });

            Route::prefix(__('url-params'))->group(function () {
                Route::get('/{name}', 'Apps\AppsReportController@getParams')->name('report.app.params.get');
                Route::any('/{name}/'.__('save'), 'Apps\AppsReportController@setParams')->name('report.app.params.set');
            });
        });

        Route::prefix(__('routes'))->group(function () {
            /* Route report */
            Route::prefix(__('route-report'))->group(function () {
                Route::get('/', function(){
                    return redirect(route('report-dispatch'));
                })->name('report-route');
                Route::get('/show', 'ReportRouteController@show')->name('report-route-search');
                Route::any(__('url-chart') . '/{dispatchRegister}', 'ReportRouteController@chart')->name('report-route-chart');
                Route::any(__('url-chart') . '/{dispatchRegister}/{location}', 'ReportRouteController@chartView')->name('report-route-chart-view');
                Route::any('/off_road/{dispatchRegister}', 'ReportRouteController@offRoadReport')->name('report-route-off-road');
                Route::any('/get-log/{dispatchRegister}', 'ReportRouteController@getReportLog')->name('report-route-get-log');
                Route::any('/ajax', 'ReportRouteController@ajax')->name('route-ajax-action');
            });

            /* Route report */
            Route::prefix(__('dispatch'))->group(function () {
                Route::get('/', 'ReportRouteController@index')->name('report-dispatch');
                Route::get('/show', 'ReportRouteController@show')->name('report-dispatch-search');
                Route::any(__('url-chart') . '/{dispatchRegister}', 'ReportRouteController@chart')->name('report-dispatch-chart');
                Route::any(__('url-chart') . '/{dispatchRegister}/{location}', 'ReportRouteController@chartView')->name('report-dispatch-chart-view');
                Route::any('/off_road/{dispatchRegister}', 'ReportRouteController@offRoadReport')->name('report-dispatch-off-road');
                Route::any('/get-log/{dispatchRegister}', 'ReportRouteController@getReportLog')->name('report-dispatch-get-log');
                Route::any('/ajax', 'ReportRouteController@ajax')->name('report-dispatch-action');
            });

            Route::prefix(__('url-historic'))->group(function () {
                Route::get('/', 'ReportRouteHistoricController@index')->name('report-route-historic');
                Route::get('/show', 'ReportRouteHistoricController@show')->name('report-route-historic-search');
            });

            /* Off Road report */
            Route::prefix(__('off-road'))->group(function () {
                Route::get('/', 'ReportRouteOffRoadController@index')->name('report-route-off-road-index');
                Route::get('/show', 'ReportRouteOffRoadController@searchReport')->name('report-route-off-road-search');
                Route::get('/address/{location}', 'ReportRouteOffRoadController@getAddressFromCoordinates')->name('report-route-off-road-geolocation-address');
                Route::get('/image/{location}', 'ReportRouteOffRoadController@getImageFromCoordinate')->name('report-route-off-road-geolocation-image');
                Route::post('/is-fake/{location}', 'ReportRouteOffRoadController@markLocationAsFakeOffRoad')->name('report-route-off-road-is-fake');
            });

            /* Control Points report */
            Route::prefix(__('control-points'))->group(function () {
                Route::get('/', 'ControlPointsReportController@index')->name('report-route-control-points');
                Route::get('/show', 'ControlPointsReportController@searchReport')->name('report-route-control-points-search-report');
            });

            /* Control Points report */
            Route::prefix(__('dispatch-users'))->group(function () {
                Route::get('/', 'ReportRouteDispatchUsersController@index')->name('report-route-dispatch-users');
                Route::get('/show', 'ReportRouteDispatchUsersController@show')->name('report-route-dispatch-users-show');
            });
        });

        Route::prefix(__('url-vehicles'))->group(function () {
            /* Off Road report */
            Route::prefix(__('parked'))->group(function () {
                Route::get('/', 'ParkedVehiclesReportController@index')->name('report-vehicle-parked');
                Route::get('/show', 'ParkedVehiclesReportController@searchReport')->name('report-vehicle-parked-search-report');
                Route::get('/address/{parkingReport}', 'ParkedVehiclesReportController@getAddressFromCoordinates')->name('report-vehicle-parked-geolocation-address');
                Route::get('/image/{parkingReport}', 'ParkedVehiclesReportController@getImageFromCoordinate')->name('report-vehicle-parked-geolocation-image');
            });

            /* Speeding report */
            Route::prefix(__('speeding'))->group(function () {
                Route::get('/', 'SpeedingReportController@index')->name('report-vehicle-speeding');
                Route::get('/show', 'SpeedingReportController@show')->name('report-vehicle-speeding-search-report');
                Route::get('/address/{location}', 'SpeedingReportController@getAddressFromCoordinates')->name('report-vehicle-speeding-geolocation-address');
                Route::get('/image/{location}', 'SpeedingReportController@getImageLocationFromCoordinates')->name('report-vehicle-speeding-geolocation-image');
            });

            /* Mileage report */
            Route::prefix(__('mileage'))->group(function () {
                Route::prefix(__('daily'))->group(function () {
                    Route::get('/', 'ReportMileageController@index')->name('report-vehicle-mileage');
                    Route::get('/show', 'ReportMileageController@show')->name('report-vehicle-mileage-show');
                });

                Route::prefix(__('date-range'))->group(function () {
                    Route::get('/', 'ReportMileageDateRangeController@index')->name('report-vehicle-mileage-date-range');
                    Route::get('/show', 'ReportMileageDateRangeController@show')->name('report-vehicle-mileage-show-date-range');
                });
            });

            /* Mileage report */
            Route::prefix(__('round-trips'))->group(function () {
                Route::get('/', 'ReportVehicleRoundTripsController@index')->name('report-vehicle-round-trips');
                Route::get('/show', 'ReportVehicleRoundTripsController@show')->name('report-vehicle-round-trips-show');
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
            });

            /* Router for recorders reports */
            Route::prefix(__('recorders'))->group(function () {
                /* Router for General Reports */
                Route::prefix(__('consolidated'))->group(function () {
                    Route::prefix(__('daily'))->group(function () {
                        Route::get('/', 'ReportPassengerRecorderConsolidatedDailyController@index')->name('report-passengers-recorders-consolidated-daily');
                        Route::get('/show', 'ReportPassengerRecorderConsolidatedDailyController@show')->name('report-passengers-recorders-consolidated-daily-search');
                        Route::get('/export', 'ReportPassengerRecorderConsolidatedDailyController@export')->name('report-passengers-recorders-consolidated-daily-export');
                    });

                    Route::prefix(__('date-range'))->group(function () {
                        Route::get('/', 'PassengersReportDateRangeController@index')->name('report-passengers-recorders-consolidated-date-range');
                        Route::get('/show', 'PassengersReportDateRangeController@show')->name('report-passengers-recorders-consolidated-date-range-search');
                        Route::get('/export', 'PassengersReportDateRangeController@export')->name('report-passengers-recorders-consolidated-date-range-export');
                    });
                });

                /* Router for General Reports */
                Route::prefix(__('detailed'))->group(function () {
                    Route::prefix(__('daily'))->group(function () {
                        Route::get('/', 'PassengerReportDetailedController@index')->name('report-passengers-recorders-detailed-daily');
                        Route::get('/show', 'PassengerReportDetailedController@show')->name('report-passengers-recorders-detailed-daily-search');
                        Route::get('/export', 'PassengerReportDetailedController@export')->name('report-passengers-recorders-detailed-daily-export');
                    });

                    Route::prefix(__('date-range'))->group(function () {
                        Route::get('/', 'PassengerReportDetailedDateRangeController@index')->name('report-passengers-recorders-detailed-date-range');
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

                Route::prefix(__('url-cameras'))->group(function () {
                    Route::get('/', 'CobanCameraController@index')->name('report-passengers-sensors-cameras');
                    Route::get('/search-params', 'CobanCameraController@searchParams')->name('report-passengers-sensors-cameras-search-params');
                    Route::get('/show', 'CobanCameraController@show')->name('report-passengers-sensors-cameras-show');
                    Route::get('/photo/{photo}', 'CobanCameraController@showPhoto')->name('report-passengers-sensors-cameras-photo');
                });
            });

            Route::prefix(__('mixed'))->group(function () {
                Route::get('/', 'PassengersMixedReportController@index')->name('report-passengers-mixed');
                Route::get('/search', 'PassengersMixedReportController@show')->name('report-passengers-mixed-search');
            });

            Route::prefix(__('url-geolocation'))->group(function () {
                Route::get('/', 'GeolocationPassengersReportController@index')->name('report-passengers-geolocation');
                Route::any('/search', 'GeolocationPassengersReportController@search')->name('report-passengers-geolocation-search');
            });
        });

        /* Routes for drivers report */
        Route::prefix(__('drivers'))->group(function () {
            Route::prefix(__('consolidated'))->group(function () {
                Route::get('/', 'DriverConsolidatedController@index')->name('report-drivers-consolidated');
                Route::get('/show', 'DriverConsolidatedController@show')->name('report-drivers-consolidated-search');
                Route::get('/export', 'DriverConsolidatedController@export')->name('report-drivers-consolidated-export');
            });

            /* Router for General Reports */
            Route::prefix(__('detailed'))->group(function () {
                Route::get('/', 'DriverDetailedController@index')->name('report-drivers-detailed');
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

    /* Routes for operation pages */
    Route::prefix(__('takings'))->group(function () {
        Route::prefix(__('passengers'))->group(function () {

            Route::prefix(__('search'))->group(function () {
                Route::get(__('url-liquidation'), 'TakingsPassengersLiquidationController@searchLiquidation')->name('takings-passengers-search-liquidation');
                Route::get(__('takings'), 'TakingsPassengersLiquidationController@searchTakings')->name('takings-passengers-search-takings');
                Route::get(__('takings').'/list', 'TakingsPassengersLiquidationController@searchTakingsList')->name('takings-passengers-search-takings-list');
                Route::get('/file/discount/{id}', 'TakingsPassengersLiquidationController@getFileDiscount')->name('takings-passengers-search-file-discount');
            });

            Route::prefix(__('url-liquidation'))->group(function () {
                Route::get('/', 'TakingsPassengersLiquidationController@index')->name('takings-passengers-liquidation');
                Route::post('/liquidate', 'TakingsPassengersLiquidationController@liquidate')->name('takings-passengers-liquidation-liquidate');
                Route::post('/update/{liquidation}', 'TakingsPassengersLiquidationController@updateLiquidation')->name('takings-passengers-liquidation-update');
                Route::get('/export/'.__('Receipt').'-{liquidation}', 'TakingsPassengersLiquidationController@exportLiquidation')->name('takings-passengers-liquidation-export');
                Route::get('/test', 'TakingsPassengersLiquidationController@test')->name('takings-passengers-liquidation-test');
            });

            Route::prefix(__('url-params'))->group(function () {
                Route::get('/{name}', 'TakingsPassengersLiquidationController@getParams')->name('takings-passengers-liquidation-params');
                Route::post('/{name}/'.__('save'), 'TakingsPassengersLiquidationController@setParams')->name('takings-passengers-liquidation-params-set');
                Route::post('/{name}/'.__('delete'), 'TakingsPassengersLiquidationController@setParams')->name('takings-passengers-liquidation-params-delete');
            });

            Route::post('takings/{liquidation}', 'TakingsPassengersLiquidationController@takings')->name('taking-passengers-takings');

            Route::prefix(__('report'))->group(function () {
                Route::get(__('daily'), 'TakingsPassengersLiquidationController@searchDailyReport')->name('takings-passengers-report-daily');
                Route::get(__('daily')."/export", 'TakingsPassengersLiquidationController@exportDailyReport')->name('takings-passengers-report-daily-export');
            });
        });
    });

    /****************** MIGRATION ROUTES *******************/

    /* Routes for migrate Tables */
    Route::prefix(__('migrate'))->group(function () {
        Route::get('/', 'MigrationController@index')->name('migrate');
        Route::get('/companies', 'MigrationController@migrateCompanies')->name('migrate-companies');
        Route::get('/routes', 'MigrationController@migrateRoutes')->name('migrate-routes');
        Route::get('/dispatches', 'MigrationController@migrateDispatches')->name('migrate-dispatches');
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
            Route::post('/upload-kmz', 'MigrationControlPointController@uploadKmz')->name('migrate-cp-upload-kmz');
            Route::get('/download-kmz/{route}', 'MigrationControlPointController@downloadKmz')->name('migrate-cp-download-kmz');
            Route::get('/calibrate/{route}/{apply}', 'MigrationControlPointController@calibrateRoute')->name('migrate-cp-calibrate-route');
        });
    });

    /* Routes for tools */
    Route::prefix(__('tools'))->group(function () {
        Route::get('/check-gps-limbo', 'ToolsController@checkGPSLimbo')->name('tools-check-gps-limbo');

        Route::prefix(__('map'))->group(function () {
            Route::get('/', 'ToolsController@map')->name('tools-map');
            Route::get('/test', 'ToolsController@test')->name('tools-map-test');
            Route::get('/get-route-distance/{route}', 'ToolsController@getRouteDistance')->name('tools-map-get-route-distance');
        });

        Route::prefix(__('smart-recovery'))->group(function () {
            Route::get('/', 'ToolsController@smartRecovery')->name('tools-smart-recovery');
        });

        Route::prefix(__('send-mail-reports'))->group(function () {
            Route::get('/', 'ToolsController@sendMailReports')->name('tools-send-mail-reports');
        });

        Route::prefix(__('scripts'))->group(function () {
            Route::get('/{gps}', 'ToolsController@showScript')->name('tools-scripts');
        });
    });
});

Route::prefix(__('link'))->group(function () {
    Route::any(__('reports') . '/' . __('routes') . '/' . __('url-chart') . '/{dispatchRegister}/{location}', function ($d, $l) {
        $link = route('report-route-chart-view', ['dispatchRegister' => $d, 'location' => $l]);
        return view('reports.route.route.templates._externalLinks', compact('link'));
    })->name('link-report-route-chart-view');

    // Url temporal. Because excel url on consolidated mail report was generated with url for historic view, instead of url for chart view on the month of March
    // TODO: Delete next code on July 2019
    Route::any(__('reports') . '/' . __('routes') . '/' . __('url-historic') . '/{dispatchRegister}', function (Illuminate\Http\Request $request, $first) {
        $user = User::find($first);
        if(!$user){
            return redirect(route('report-route-chart-view',['dispatchRegister' => $first, 'location' => 0]));
        }
        Auth::login($user, true);
        return redirect(route('report-route-historic'));
    });

    Route::any(__('reports') . '/' . __('routes') . '/' . __('url-historic-path') . '/{user}', function (User $user) {
        Auth::login($user, true);
        return redirect(route('report-route-historic'));
    })->name('link-report-route-historic-path');

    // TODO: Temporal link to render beta ML on NE
    Route::prefix(__('takings'))->group(function () {
        Route::prefix(__('passengers'))->group(function () {
            Route::prefix(__('url-liquidation'))->group(function () {
                Route::get('/{user}', function (User $user){
                    Auth::login($user, true);

                    return redirect(route('takings-passengers-liquidation'))->with('hide-menu', true);
                })->name('link-takings-passengers-liquidation');
            });
        });
    });
});

