<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\StrTime;
use App\Models\Routes\Dispatch;
use App\Models\Routes\RouteTariff;
use Auth;
use App\CobanVehicle;
use App\Models\Company\Company;
use App\Models\Routes\ControlPoint;
use App\Models\Routes\ControlPointTime;
use App\Models\Routes\Fringe;
use App\Models\Vehicles\GpsVehicle;
use App\Models\Routes\Route;
use App\Models\Routes\RouteGoogle;
use App\Models\Users\User;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MigrationController extends Controller
{
    const OLD_TABLES = [
        'companies' => 'empresa',
        'routes' => 'ruta',
        'dispatches' => 'despachos',
        'users' => 'acceso',
        'vehicles' => 'crear_vehiculo',
        'control_points' => 'puntos_control_ruta',
        'fringes' => 'franjas_rutas',
        'control_point_times' => 'tiempos_punto_control',
    ];

    const ROUTES_FOR_MIGRATE = [124, 125, 126, 127, 128, 129, 135, 136, 137, 139, 141, 144, 145, 146, 151, 154, 155, 156, 158, 159, 161, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 191, 192, 193, 198, 199, 201, 202, 203, 206, 207, 210, 213, 214, 215, 216, 218, 219, 221, 222, 223, 224, 228, 229, 230, 231, 232, 233, 234, 235, 236, 237, 238, 242, 243];

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getRoutesForMigrate(Request $request)
    {
        $routesForMigrate = collect(self::ROUTES_FOR_MIGRATE);
        $routesForMigrate = $routesForMigrate->merge(collect(DB::select("SELECT id_rutas FROM ruta WHERE migrate IS TRUE"))->pluck('id_rutas')->toArray());

        $routeFromRequest = $request->get('route');
        if ($routeFromRequest) {
            $routesForMigrate = $routesForMigrate->filter(function ($value, $key) use ($routeFromRequest) {
                return intval($value) === intval($routeFromRequest);
            });
        }

        return $routesForMigrate->toArray();
    }

    /**
     * Show the application dashboard.
     *
     */
    public function index(Request $request)
    {
        if (!Auth::user()->isAdmin()) abort(403);

        $route = $request->get('route');

        $tables = collect([]);

        if (!$route) {
            $tables->push((object)[
                'name' => self::OLD_TABLES['companies'],
                'route' => route('migrate-companies'),
                'total' => DB::table(self::OLD_TABLES['companies'])->count(),
                'total_migrated' => Company::count()
            ]);

            $tables->push((object)[
                'name' => self::OLD_TABLES['users'],
                'route' => route('migrate-users'),
                'total' => DB::table(self::OLD_TABLES['users'])->count(),
                'total_migrated' => User::count()
            ]);

            $tables->push((object)[
                'name' => self::OLD_TABLES['vehicles'],
                'route' => route('migrate-vehicles'),
                'total' => DB::table(self::OLD_TABLES['vehicles'])->count(),
                'total_migrated' => Vehicle::count()
            ]);
        }

        $tables->push((object)[
            'name' => self::OLD_TABLES['dispatches'],
            'route' => route('migrate-dispatches'),
            'total' => DB::table(self::OLD_TABLES['dispatches'])->count(),
            'total_migrated' => Dispatch::count()
        ]);

        $tables->push((object)[
            'name' => self::OLD_TABLES['routes'],
            'route' => route('migrate-routes'),
            'total' => DB::table(self::OLD_TABLES['routes'])->whereIn('id_rutas', $this->getRoutesForMigrate($request))->count(),
            'total_migrated' => Route::whereIn('id', $this->getRoutesForMigrate($request))->count()
        ]);

        $tables->push((object)[
            'name' => self::OLD_TABLES['control_points'],
            'route' => route('migrate-control-points'),
            'total' => DB::table(self::OLD_TABLES['control_points'])->whereIn('id_ruta', $this->getRoutesForMigrate($request))->count(),
            'total_migrated' => ControlPoint::whereIn('route_id', $this->getRoutesForMigrate($request))->count()
        ]);

        $tables->push((object)[
            'name' => self::OLD_TABLES['fringes'],
            'route' => route('migrate-fringes'),
            'total' => DB::table(self::OLD_TABLES['fringes'])->whereIn('id_ruta', $this->getRoutesForMigrate($request))->count(),
            'total_migrated' => Fringe::whereIn('route_id', $this->getRoutesForMigrate($request))->count()
        ]);

        $tables->push((object)[
            'name' => self::OLD_TABLES['control_point_times'],
            'route' => route('migrate-control-point-times'),
            'total' => DB::table(self::OLD_TABLES['control_point_times'])->whereIn('id_ruta', $this->getRoutesForMigrate($request))->count(),
            'total_migrated' => ControlPointTime::whereIn('control_point_id', ControlPoint::whereIn('route_id', $this->getRoutesForMigrate($request))->get()->pluck('id'))->count()
        ]);

        return view('migrations.tables', compact(['tables', 'route']));
    }


    public function migrateCompanies(Request $request, $exit = true)
    {
        /*if ($request->get('delete')) {
            $deleted = DB::delete('DELETE FROM companies');
            dd($deleted . ' registers has ben deleted!');
        }*/

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $companies = DB::table(self::OLD_TABLES['companies'])->get();

        $migrateCompany = $request->get('company');

        if ($migrateCompany) {
            $companies = $companies->where('id_empresa', $migrateCompany);
        }

        foreach ($companies as $companyOLD) {
            $new = false;
            $company = Company::find($companyOLD->id_empresa);
            if (!$company) {
                $company = new Company();
                $new = true;
            }
            $company->id = $companyOLD->id_empresa;
            $company->name = $companyOLD->des_empresa;
            $company->short_name = $companyOLD->des_corta;
            $company->nit = $companyOLD->nit;
            $company->address = $companyOLD->direccion;
            $company->link = $companyOLD->url;
            $company->timezone = $companyOLD->timezone;
            $company->active = $companyOLD->estado && !$companyOLD->observaciones;
            $company->default_kmz_url = $companyOLD->default_kmz_url;

            try {
                $company->save();
                $new ? $totalCreated++ : $totalUpdated++;
            } catch (QueryException $e) {
                $totalErrors++;
                dump($e->getMessage());
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e->getMessage());
            }
        }

        if ($exit) {
            dd([
                'Total Created' => $totalCreated,
                'Total Updated' => $totalUpdated,
                'Total Errors' => $totalErrors
            ]);
        }
    }

    public function migrateRoutes(Request $request)
    {
        /*if ($request->get('delete')) {
            $deleted = DB::delete('DELETE FROM routes');
            dd($deleted . ' registers has ben deleted!');
        }*/

        $this->migrateCompanies($request, false);
        $this->migrateDispatches($request, false);

        DB::statement("
            UPDATE ruta SET distancia = (SELECT (distance_from_dispatch/1000)::INTEGER
            FROM control_points WHERE route_id = ruta.id_rutas
            ORDER BY distance_from_dispatch DESC LIMIT 1) WHERE id_rutas IN (" . implode(',', $this->getRoutesForMigrate($request)) . ")
        ");

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $routes = DB::table(self::OLD_TABLES['routes'])->whereIn('id_rutas', $this->getRoutesForMigrate($request))->get();

        $migrateCompany = $request->get('company');

        if ($migrateCompany) {
            $routes = $routes->where('id_empresa', $migrateCompany);
        }

        foreach ($routes as $routeOLD) {
            $new = false;
            $route = Route::find($routeOLD->id_rutas);
            if (!$route) {
                $route = new Route();
                $new = true;
            }
            $route->id = $routeOLD->id_rutas;
            $route->name = $routeOLD->nombre;
            $route->distance = $routeOLD->distancia ?? 0;
            $route->min_route_time = $routeOLD->min_route_time;
            $route->company_id = $routeOLD->id_empresa;
            $route->dispatch_id = $routeOLD->id_despacho;
            $route->active = $routeOLD->estado == 0 ? true : false;
            $route->as_group = $routeOLD->as_group;
            $route->route_id = $routeOLD->route_id;

            if($route->id == 204) {
                dump($routeOLD);
            }

            $routeGoogle = RouteGoogle::find($route->id);
            $route->url = $routeGoogle ? $routeGoogle->url : "";

            try {
                $route->save();
                $route->refresh();
                $routeTariff = $route->tariff;
                if (!$routeTariff) {
                    $routeTariff = new RouteTariff();
                    $routeTariff->route_id = $route->id;
                }
                $tariffValue = collect(DB::select("SELECT tarifa passenger, fuel_tariff fuel FROM tarifas_rutas WHERE id_ruta = $route->id"))->first();
                $routeTariff->passenger = $tariffValue ? $tariffValue->passenger : 0;
                $routeTariff->fuel = $tariffValue ? $tariffValue->fuel : 0;
                $routeTariff->save();

                $new ? $totalCreated++ : $totalUpdated++;
            } catch (QueryException $e) {
                $totalErrors++;
                dump($e->getMessage());
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e->getMessage());
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateDispatches(Request $request, $exit = true)
    {
        if ($request->get('delete')) {
            $deleted = DB::delete('DELETE FROM dispatches');
            dd($deleted . ' registers has ben deleted!');
        }

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $dispatches = DB::table(self::OLD_TABLES['dispatches'])->get();

        $migrateCompany = $request->get('company');

        if ($migrateCompany) {
            $dispatches = $dispatches->where('id_empresa', $migrateCompany);
        }

        foreach ($dispatches as $dispatchOLD) {
            $new = false;
            $dispatch = Dispatch::find($dispatchOLD->id_despachos);
            if (!$dispatch) {
                $dispatch = new Dispatch();
                $new = true;
            }
            $dispatch->id = $dispatchOLD->id_despachos;
            $dispatch->name = $dispatchOLD->nombre;
            $dispatch->latitude = $dispatchOLD->latitude;
            $dispatch->longitude = $dispatchOLD->longitude;
            $dispatch->company_id = $dispatchOLD->id_empresa;
            $dispatch->active = $dispatchOLD->estado == 0 ? true : false;
            $dispatch->radio_geofence = $dispatchOLD->radio_geofence;

            try {
                $dispatch->save();
                $new ? $totalCreated++ : $totalUpdated++;
            } catch (QueryException $e) {
                $totalErrors++;
                dump($e->getMessage());
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e->getMessage());
            }
        }
        if ($exit) {
            dd([
                'Total Created' => $totalCreated,
                'Total Updated' => $totalUpdated,
                'Total Errors' => $totalErrors
            ]);
        }
    }

    public function migrateUsers(Request $request)
    {
        if ($request->get('delete')) {
//            $deleted = DB::delete('DELETE FROM users');
//            dd($deleted . ' registers has ben deleted!');
            dd('Not allowed');
        }

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $users = DB::table(self::OLD_TABLES['users'])->get();

        $migrateCompany = $request->get('company');

        if ($migrateCompany) {
            $users = $users->where('id_empresa', $migrateCompany);
        }

        foreach ($users as $userOLD) {
            $new = false;
            $user = User::find($userOLD->id_usuario);
            if (!$user) {
                $user = new User();
                $new = true;
            }
            $user->id = $userOLD->id_usuario;
            $user->name = $userOLD->primer_nombre . ($userOLD->primer_apellido ? ' ' . $userOLD->primer_apellido : '');
            $user->email = $userOLD->correo;
            $user->username = $userOLD->usuario;
            $user->password = bcrypt($userOLD->clave);
            $user->role = $userOLD->nombre;
            $user->active = $userOLD->estado;
            $user->company_id = $userOLD->id_empresa;
            $user->role_id = $userOLD->nivel;
            $user->vehicle_tags = $userOLD->vehicle_tags;

            try {
                $user->save();
                $new ? $totalCreated++ : $totalUpdated++;
            } catch (QueryException $e) {
                $totalErrors++;
                dump($e->getMessage());
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e->getMessage());
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateVehicles(Request $request)
    {
        if ($request->get('delete')) {
//            $deleted = DB::delete('DELETE FROM gps_vehicles');
//            dump($deleted . ' registers has ben deleted from gps_vehicles!');
//            $deleted = DB::delete('DELETE FROM vehicles');
//            dd($deleted . ' registers has ben deleted!');
        }

        $maxIdGpsVehicles = collect(DB::select("SELECT max(id) FROM gps_vehicles"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE gps_vehicles_id_seq RESTART WITH $maxIdGpsVehicles");
        $maxIdSimGps = collect(DB::select("SELECT max(id) FROM sim_gps"))->first()->max + 1;
        DB::statement("ALTER SEQUENCE sim_gps_id_seq RESTART WITH $maxIdSimGps");

        /* For vehicles table */
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;

        /* For gps_vehicles table */
        $gpsVehicleTotalCreated = 0;
        $gpsVehicleTotalUpdated = 0;
        $gpsVehicleTotalErrors = 0;

        $vehicles = DB::table(self::OLD_TABLES['vehicles'])->get();
        $migrateCompany = $request->get('company');
        $migrateVehicle = $request->get('vehicle');

        if ($migrateCompany) {
            $vehicles = $vehicles->where('empresa', $migrateCompany);
        }
        if ($migrateVehicle) {
            $vehicles = $vehicles->where('id_crear_vehiculo', $migrateVehicle);
        }


        foreach ($vehicles as $vehicleOLD) {
            $new = false;
            $vehicle = Vehicle::find($vehicleOLD->id_crear_vehiculo);
            if (!$vehicle) {
                $vehicle = new Vehicle();
                $new = true;
            }
            $vehicle->id = $vehicleOLD->id_crear_vehiculo;
            $vehicle->plate = $vehicleOLD->placa;
            $vehicle->number = $vehicleOLD->num_vehiculo;
            $vehicle->company_id = $vehicleOLD->empresa;
            $vehicle->active = $vehicleOLD->estado == 1;
            $vehicle->in_repair = $vehicleOLD->en_taller == 1;
            $vehicle->observations = $vehicleOLD->observaciones;
            $vehicle->proprietary_id = $vehicleOLD->proprietary_id;
            $vehicle->driver_id = $vehicleOLD->conductor_id;
            $vehicle->tags = $vehicleOLD->tags;

            $vehicle->save();

            try {
                $new ? $totalCreated++ : $totalUpdated++;

                /* Migrate data for gps_vehicle */

                $gpsVehicleNew = false;
                $gpsVehicle = GpsVehicle::whereVehicleId($vehicleOLD->id_crear_vehiculo)->get()->first();
                if (!$gpsVehicle) {
                    $gpsVehicle = new GpsVehicle();
                    $gpsVehicleNew = true;
                }
                $gpsVehicle->imei = ($vehicleOLD->imei_gps && $vehicleOLD->imei_gps != 0) ? $vehicleOLD->imei_gps : $vehicleOLD->placa;
                $gpsVehicle->vehicle_id = $vehicleOLD->id_crear_vehiculo;

                try {
                    $gpsVehicle->save();
                    $gpsVehicleNew ? $gpsVehicleTotalCreated++ : $gpsVehicleTotalUpdated++;
                } catch (Exception $e_gps) {
                    $gpsVehicleTotalErrors++;
                    dd('GPS VEHICLE ERROR: ', $e_gps->getMessage());
                }
            } catch (QueryException $e) {
                $totalErrors++;
                // dump($e->getMessage());
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e->getMessage());
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors,
            'Company' => $migrateCompany,
            'Vehicle' => $migrateVehicle,
            '------------------------------',
            'Gps Vehicle Total Created' => $gpsVehicleTotalCreated,
            'Gps Vehicle Total Updated' => $gpsVehicleTotalUpdated,
            'Gps Vehicle Total Errors' => $gpsVehicleTotalErrors,
        ]);
    }

    public function migrateControlPoints(Request $request = null)
    {
        DB::statement("ALTER TABLE control_point_time_reports DISABLE TRIGGER ALL");
        DB::statement("ALTER TABLE control_point_times DISABLE TRIGGER ALL");

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $controlPoints = DB::table(self::OLD_TABLES['control_points'])->whereIn('id_ruta', $this->getRoutesForMigrate($request))->get();
        foreach ($controlPoints as $controlPointOLD) {
            $new = false;
            $controlPoint = ControlPoint::find($controlPointOLD->secpuntos_control_ruta);
            if (!$controlPoint) {
                $controlPoint = new ControlPoint();
                $new = true;
            }
            $controlPoint->id = $controlPointOLD->secpuntos_control_ruta;
            $controlPoint->latitude = $controlPointOLD->lat;
            $controlPoint->longitude = $controlPointOLD->lng;
            $controlPoint->name = $controlPointOLD->nombre;
            $controlPoint->trajectory = $controlPointOLD->trayecto;
            $controlPoint->order = $controlPointOLD->orden;
            $controlPoint->type = $controlPointOLD->tipo;
            if ($new) { // Only when create new register because the logic calibration on GRAILS NE manage this values
                $controlPoint->distance_from_dispatch = $controlPointOLD->distancia_desde_despacho;
                $controlPoint->distance_next_point = intval($controlPointOLD->distancia_punto_siguiente);
            }
            $controlPoint->route_id = $controlPointOLD->id_ruta;

            try {
                $controlPoint->save();
                $new ? $totalCreated++ : $totalUpdated++;
            } catch (QueryException $e) {
                $totalErrors++;
                dump($e->getMessage());
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e->getMessage());
            }
        }

        DB::statement("ALTER TABLE control_point_times ENABLE TRIGGER ALL");
        //DB::statement("ALTER TABLE fringes ENABLE TRIGGER ALL");
        DB::statement("ALTER TABLE control_point_time_reports ENABLE TRIGGER ALL");

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateFringes(Request $request)
    {
        DB::statement("ALTER TABLE control_point_times DISABLE TRIGGER ALL");
        //DB::statement("ALTER TABLE fringes DISABLE TRIGGER ALL");

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;

        $fringes = DB::table(self::OLD_TABLES['fringes'])
            ->whereIn('id_ruta', $this->getRoutesForMigrate($request))
            ->get();

        foreach ($fringes as $fringeOLD) {
            for ($i = 1; $i <= 15; $i++) {
                $fringeI = $fringeOLD->{"franja_$i"};
                if ($fringeI == "") break;

                $fringeTime = explode(" a ", $fringeI);
                $uid = "$fringeOLD->id_ruta-$fringeOLD->tipo_de_dia-$i";

                $new = false;
                $fringe = Fringe::where('uid', $uid)->get()->first();
                if (!$fringe) {
                    $fringe = new Fringe();
                    $new = true;
                }

                $fringe->name = $fringeI;
                $fringe->from = $fringeTime[0];
                $fringe->to = $fringeTime[1] ? "$fringeTime[1]:59" : '00:00:00';
                $fringe->sequence = $i;
                $fringe->active = true;
                $fringe->route_id = $fringeOLD->id_ruta;
                $fringe->day_type_id = $fringeOLD->tipo_de_dia;
                $fringe->style_color = "#" . substr(md5(rand()), 0, 6);
                $fringe->uid = $uid;

                try {
                    $fringe->save();
                    $new ? $totalCreated++ : $totalUpdated++;
                } catch (QueryException $e) {
                    $totalErrors++;
                    dump($e->getMessage());
                } catch (\PDOException $e) {
                    $totalErrors++;
                    dump($e->getMessage());
                }
            }
        }

        DB::statement("ALTER TABLE control_point_times ENABLE TRIGGER ALL");
        //DB::statement("ALTER TABLE fringes ENABLE TRIGGER ALL");

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateControlPointTimes(Request $request)
    {
        DB::statement("ALTER TABLE control_point_times DISABLE TRIGGER ALL");
        DB::statement("ALTER TABLE tiempos_punto_control DISABLE TRIGGER ALL");

        //DB::statement("SELECT pg_catalog.setval('control_point_times_id_seq', 1, false)");

        DB::statement("UPDATE tiempos_punto_control SET tiempo1 = '00:00' WHERE tiempo1 = ''");
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;

        foreach ($this->getRoutesForMigrate($request) as $route) {
            $controlPointTimesByDays = collect(
                DB::select("
                    SELECT
                      tpc.id_tiempos_punto_control id,
                      tpc.id_punto_control control_point_id,
                      pc.orden,
                      pc.nombre,
                      tpc.tiempo1 time1,
                      tpc.tiempo2 time2,
                      tpc.tiempo3 time3,
                      tpc.tiempo4 time4,
                      tpc.tiempo5 time5,
                      tpc.tiempo6 time6,
                      tpc.tiempo7 time7,
                      tpc.tiempo8 time8,
                      tpc.tiempo9 time9,
                      tpc.tiempo10 time10,
                      tpc.tiempo11 time11,
                      tpc.tiempo12 time12,
                      tpc.tiempo13 time13,
                      tpc.tiempo14 time14,
                      tpc.tiempo15 time15,
                      tpc.tipo_dia day_type,
                      tpc.id_ruta route_id
                    FROM
                      puntos_control_ruta AS pc,
                      tiempos_punto_control AS tpc
                    WHERE tpc.id_punto_control = pc.secpuntos_control_ruta AND pc.id_ruta = $route
                    GROUP BY pc.secpuntos_control_ruta, tpc.id_tiempos_punto_control
                    ORDER BY tpc.tipo_dia, pc.orden"
                )
            )->groupBy('day_type');

            foreach ($controlPointTimesByDays as $controlPointTimes) {
                for ($i = 1; $i <= 15; $i++) {
                    $day_type = 0;
                    $last_time = "00:00:00";

                    foreach ($controlPointTimes as $index => $controlPointTimesOLD) {
                        if ($day_type != $controlPointTimesOLD->day_type) {
                            $day_type = $controlPointTimesOLD->day_type;
                            $last_time = "00:00:00";
                        }

                        $timeFringeI = $controlPointTimesOLD->{"time$i"};

                        $fringe = Fringe::where('uid', "$route-$day_type-$i")->get()->first();

                        if ($fringe && $timeFringeI != "") {

                            $new = false;
                            $uid = "$controlPointTimesOLD->control_point_id-$fringe->id";

                            $controlPointTime = ControlPointTime::where('uid', $uid)->get()->first();

                            if (!$controlPointTime) {
                                $controlPointTime = new ControlPointTime();
                                $new = true;
                            }

                            $controlPointTime->time = StrTime::intervalToTime($timeFringeI);
                            $controlPointTime->time_next_point = StrTime::intervalToTime((($index == (count($controlPointTimes) - 1)) ? $controlPointTime->time : $controlPointTimes[$index + 1]->{"time$i"}));
                            $controlPointTime->time_from_dispatch = StrTime::intervalToTime(date("H:i:s", strtotime($last_time) + strtotime($controlPointTime->time) - strtotime("00:00:00")));
                            $controlPointTime->day_type_id = $day_type;
                            $controlPointTime->control_point_id = $controlPointTimesOLD->control_point_id;
                            $controlPointTime->fringe_id = $fringe->id;
                            $controlPointTime->uid = $uid;

                            $last_time = $controlPointTime->time_from_dispatch;

                            try {
                                $controlPointTime->save();
                                $new ? $totalCreated++ : $totalUpdated++;
                            } catch (QueryException $e) {
                                $totalErrors++;
                                dump($e->getMessage());
                            } catch (\PDOException $e) {
                                $totalErrors++;
                                dump($e->getMessage());
                            }
                        }
                    }
                }
            }
        }

        DB::statement("ALTER TABLE control_point_times ENABLE TRIGGER ALL");
        DB::statement("ALTER TABLE tiempos_punto_control ENABLE TRIGGER ALL");

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }
}
