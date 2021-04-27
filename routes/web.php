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

use App\Models\Company\Company;
use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;

Auth::routes();

Route::get('/metronic', function () {
    return view('metronic');
})->name('metronic');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', function () {
        return redirect(route('report-route'));
    })->name('index');

    Route::get('/home', function () {
        return redirect(route('report-route'));
    })->name('home');

    Route::get('/info', function () {
        phpinfo();
    })->name('info');

    /* Routes for general actions */
    Route::prefix(__('general'))->group(function () {
        Route::any('/load-select-routes', 'GeneralController@loadSelectRoutes')->name('general-load-select-routes');
        Route::any('/load-select-users', 'GeneralController@loadSelectUsers')->name('general-load-select-users');
        Route::any('/load-select-control-points', 'GeneralController@loadSelectControlPoints')->name('general-load-select-control-points');
        Route::any('/load-select-fringes', 'GeneralController@loadSelectFringes')->name('general-load-select-fringes');
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

        Route::prefix(__('url-vehicles'))->group(function () {
            Route::prefix(__('vehicle-issues'))->group(function () {
                Route::get('/', function () {
                    return redirect(route('report-vehicles-issues'));
                })->name('operation-vehicles-issues');
                Route::get('/current/{company}', 'VehicleIssuesController@current')->name('operation-vehicles-issues-current');
                Route::get('/{vehicle}/form', 'VehicleIssuesController@form')->name('operation-vehicles-issues-form');
                Route::post('/{vehicle}/create', 'VehicleIssuesController@create')->name('operation-vehicles-issues-create');
                Route::get('/show', 'VehicleIssuesController@show')->name('operation-vehicles-issues-show');
                Route::get('/migrate/{company}', 'VehicleIssuesController@migrateOldReports')->name('operation-vehicles-issues-migrate');
            });

            Route::prefix(__('url-binnacle'))->group(function () {
                Route::get('/', 'Operation\Vehicles\Binnacle\BinnacleController@index')->name('operation-vehicles-binnacle');
                Route::post('/form/create', 'Operation\Vehicles\Binnacle\BinnacleController@formCreate')->name('operation-vehicles-binnacle-form-create');
                Route::post('/form/edit/{binnacle}', 'Operation\Vehicles\Binnacle\BinnacleController@formEdit')->name('operation-vehicles-binnacle-form-edit');
                Route::post('/form/delete/{binnacle}', 'Operation\Vehicles\Binnacle\BinnacleController@formDelete')->name('operation-vehicles-binnacle-form-delete');
                Route::post('/create', 'Operation\Vehicles\Binnacle\BinnacleController@create')->name('operation-vehicles-binnacle-create');
                Route::post('/update/{binnacle}', 'Operation\Vehicles\Binnacle\BinnacleController@update')->name('operation-vehicles-binnacle-update');
                Route::delete('/delete/{binnacle}', 'Operation\Vehicles\Binnacle\BinnacleController@delete')->name('operation-vehicles-binnacle-delete');
                Route::get('/show', 'Operation\Vehicles\Binnacle\BinnacleController@show')->name('operation-vehicles-binnacle-show');
            });
        });

        Route::prefix(__('routes'))->group(function () {
            Route::prefix(__('takings'))->group(function () {
                Route::get('/{dispatchRegister}', 'Operation\Routes\Takings\RouteTakingsController@form')->name('operation-routes-takings-form');
                Route::get('/{vehicle}/{date}', 'Operation\Routes\Takings\RouteTakingsController@formCreate')->name('operation-routes-takings-form-create');
                Route::post('/{dispatchRegister}/taking', 'Operation\Routes\Takings\RouteTakingsController@taking')->name('operation-routes-takings-taking');
            });
        });
    });

    /* Routes for route report */
    Route::prefix(__('reports'))->group(function () {
        /* General reports */
        Route::prefix(__('routes'))->group(function () {
            /* Route report */
            Route::prefix(__('route-report'))->group(function () {
                Route::get('/', function () {
                    return redirect(route('report-dispatch'));
                })->name('report-route');
                Route::get('/show', 'ReportRouteController@show')->name('report-route-search');
                Route::get('/ts', 'ReportRouteController@ts')->name('report-route-ts');
                Route::any(__('url-chart') . '/{dispatchRegister}', 'ReportRouteController@chart')->name('report-route-chart');
                Route::any(__('url-chart') . '/{dispatchRegister}/{location}', 'ReportRouteController@chartView')->name('report-route-chart-view');
                Route::any('/off_road/{dispatchRegister}', 'ReportRouteController@offRoadReport')->name('report-route-off-road');
                Route::any('/get-log/{dispatchRegister}', 'ReportRouteController@getReportLog')->name('report-route-get-log');
                Route::any('/ajax', 'ReportRouteController@ajax')->name('route-ajax-action');
            });

            /* Takings route report */
            Route::prefix(__('takings'))->group(function () {
                Route::get('/', 'Reports\Routes\Takings\TakingsController@index')->name('reports.routes.takings');
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
            Route::prefix(__('vehicle-issues'))->group(function () {
                Route::get('/', 'VehicleIssuesController@index')->name('report-vehicles-issues');
            });

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

            /* Status report */
            Route::prefix(__('status'))->group(function () {
                Route::get('/', 'VehicleStatusReportController@index')->name('report-vehicle-status');
                Route::get('/show', 'VehicleStatusReportController@searchReport')->name('report-vehicle-status-search-report');
                Route::get('/image/{vehicleStatusReport}', 'VehicleStatusReportController@getImageFromCoordinate')->name('report-vehicle-status-geolocation-image');
            });

            /* GPS vehicle report */
            Route::prefix(__('gps'))->group(function () {
                Route::get('/', 'VehicleGPSReportController@index')->name('report-vehicle-gps');
                Route::get('/show', 'VehicleGPSReportController@searchReport')->name('report-vehicle-gps-search-report');
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
                Route::get('/', 'PassengersRecordersReportController@index')->name('report-passengers-recorders-consolidated');
                Route::get('/range', 'PassengersRecordersReportController@index')->name('report-passengers-recorders-consolidated-date-range');
                Route::get('/show', 'PassengersRecordersReportController@show')->name('report-passengers-recorders-consolidated-date-range-search');
                Route::get('/export', 'PassengersRecordersReportController@export')->name('report-passengers-recorders-consolidated-date-range-export');

                /* Router for General Reports */
                Route::prefix(__('consolidated'))->group(function () {
                    Route::prefix(__('daily'))->group(function () {
                        Route::get('/', function () {
                            return redirect(route('report-passengers-recorders-consolidated'));
                        })->name('report-passengers-recorders-consolidated-daily');
                    });
                });
            });

            Route::prefix(__('fringes'))->group(function () {
                Route::get('/', 'PassengerReportByFringesController@index')->name('report-passengers-fringes');
                Route::get('/search', 'PassengerReportByFringesController@search')->name('report-passengers-fringes-search');
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

            Route::prefix(__('mixed'))->group(function () {
                Route::get('/', 'PassengersMixedReportController@index')->name('report-passengers-mixed');
                Route::get('/search', 'PassengersMixedReportController@show')->name('report-passengers-mixed-search');
            });

            Route::prefix(__('url-geolocation'))->group(function () {
                Route::get('/', 'GeolocationPassengersReportController@index')->name('report-passengers-geolocation');
                Route::any('/search', 'GeolocationPassengersReportController@search')->name('report-passengers-geolocation-search');
            });

            Route::prefix(__('photos'))->group(function () {
                Route::get('/', 'Rocket\ReportPhotosController@index')->name('report.passengers.photos');
            });

            Route::prefix(__('video'))->group(function () {
                Route::get('/', 'Rocket\ReportPhotosController@video')->name('report.passengers.video');
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
            Route::prefix(__('activity'))->group(function () {
                /* Routes for logs */
                Route::get('/', 'Reports\Users\Activity\UserActivityController@index')->name('report.users.activity');
                Route::get('/show', 'Reports\Users\Activity\UserActivityController@show')->name('report.users.activity.search');
                Route::get('/export/logins/{date}', 'Reports\Users\Activity\UserActivityController@exportLogins')->name('report.users.activity.export.logins');
            });
        });

        /* Booths report */
        Route::prefix(__('booths'))->group(function () {
            Route::prefix(__('historic'))->group(function () {
                /* Routes for logs */
                Route::get('/', 'Booths\BoothsHistoricReportController@index')->name('report-booths-historic');
                Route::get('/search', 'Booths\BoothsHistoricReportController@search')->name('report-booths-search');
            });
        });
    });

    /* Routes for operation pages */
    Route::prefix(__('takings'))->group(function () {
        Route::prefix(__('passengers'))->group(function () {
            Route::prefix(__('url-liquidation'))->group(function () {
                Route::get('/', 'TakingsPassengersLiquidationController@index')->name('takings-passengers-liquidation');
                Route::get('/search', 'TakingsPassengersLiquidationController@search')->name('takings-passengers-liquidation-search');
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

        Route::prefix(__('gps-total-frames'))->group(function () {
            Route::get('/{company}', 'ToolsController@gpsTotalFrames')->name('tools-gps-total-frames');
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

    /*Route::any(__('reports') . '/' . __('routes') . '/' . __('url-historic') . '/{dispatchRegister}', function (Illuminate\Http\Request $request, $first) {
        $user = \App\Models\Users\User::find($first);

        if(!$user){
            return redirect(route('report-route-chart-view',['dispatchRegister' => $first, 'location' => 0]));
        }
        Auth::login($user, true);
        return redirect(route('report-route-historic'));
    });*/


    Route::any(__('reports') . '/' . __('routes') . '/' . __('url-historic') . '/{user}', function (User $user) {
        Auth::login($user, true);
        $hideMenu = $user->company->id == App\Models\Company\Company::COOTRANSOL && $user->isDispatcher() ? true : null;

        return redirect(route('report-route-historic'))->with('hide-menu', $hideMenu);
    })->name('link-report-route-historic-path');

    Route::get(__('url-operation') . "/" . __('url-vehicles') . "/" . __('vehicle-issues') . "/current/{company}/{user}", function (Company $company, User $user) {
        Auth::login($user, true);
        return redirect(route('operation-vehicles-issues-current', ['company' => $company->id]));
    });

    Route::get(__('url-operation') . "/" . __('url-vehicles') . "/" . __('vehicle-issues') . "/{user}", function (User $user, Request $request) {
        if (Auth::guest()) Auth::login($user, true);
        return redirect(route('report-vehicles-issues'))->with(['hide-menu' => true]);
    });

    Route::post("{user}/operation/vehicles/issues/{vehicle}/create", 'VehicleIssuesController@createFromOldPlatform');
});

