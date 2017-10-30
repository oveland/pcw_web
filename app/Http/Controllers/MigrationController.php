<?php

namespace App\Http\Controllers;

use App\CobanVehicle;
use App\Company;
use App\ControlPoint;
use App\ControlPointTime;
use App\Fringe;
use App\GpsVehicle;
use App\Http\Controllers\Utils\Database;
use App\Route;
use App\RouteGoogle;
use App\User;
use App\Vehicle;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MigrationController extends Controller
{
    const OLD_TABLES = [
        'companies' => 'empresa',
        'routes' => 'ruta',
        'users' => 'acceso',
        'vehicles' => 'crear_vehiculo',
        'control_points' => 'puntos_control_ruta',
        'fringes' => 'franjas_rutas',
        'control_point_times' => 'tiempos_punto_control',
    ];

    const ROUTES_FOR_MIGRATE = [126, 135, 136, 137];
    const ROUTES_FOR_MIGRATE_CP = [124,125,126,127,128,129,155,156, 135, 136, 137];

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tables = collect([
            (object)[
                'name' => self::OLD_TABLES['companies'],
                'route' => route('migrate-companies'),
                'total' => DB::table(self::OLD_TABLES['companies'])->count(),
                'total_migrated' => Company::count()
            ],
            (object)[
                'name' => self::OLD_TABLES['routes'],
                'route' => route('migrate-routes'),
                'total' => DB::table(self::OLD_TABLES['routes'])->whereIn('id_rutas', self::ROUTES_FOR_MIGRATE_CP)->count(),
                'total_migrated' => Route::count()
            ],
            (object)[
                'name' => self::OLD_TABLES['users'],
                'route' => route('migrate-users'),
                'total' => DB::table(self::OLD_TABLES['users'])->count(),
                'total_migrated' => User::count()
            ],
            (object)[
                'name' => self::OLD_TABLES['vehicles'],
                'route' => route('migrate-vehicles'),
                'total' => DB::table(self::OLD_TABLES['vehicles'])->count(),
                'total_migrated' => Vehicle::count()
            ],
            (object)[
                'name' => self::OLD_TABLES['control_points'],
                'route' => route('migrate-control-points'),
                'total' => DB::table(self::OLD_TABLES['control_points'])->whereIn('id_ruta', self::ROUTES_FOR_MIGRATE_CP)->count(),
                'total_migrated' => ControlPoint::count()
            ],

            (object)[
                'name' => self::OLD_TABLES['fringes'],
                'route' => route('migrate-fringes'),
                'total' => DB::table(self::OLD_TABLES['fringes'])->whereIn('id_ruta', self::ROUTES_FOR_MIGRATE)->count(),
                'total_migrated' => Fringe::count()
            ],
            (object)[
                'name' => self::OLD_TABLES['control_point_times'],
                'route' => route('migrate-control-point-times'),
                'total' => DB::table(self::OLD_TABLES['control_point_times'])->whereIn('id_ruta', self::ROUTES_FOR_MIGRATE)->count(),
                'total_migrated' => ControlPointTime::count()
            ],
        ]);

        return view('migrations.tables', compact('tables'));
    }


    public function migrateCompanies(Request $request)
    {
        if ($request->get('delete')) {
            $deleted = DB::delete('DELETE FROM companies');
            dd($deleted . ' registers has ben deleted!');;
        }

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $companies = DB::table(self::OLD_TABLES['companies'])->get();
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
            $company->active = $companyOLD->estado;

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

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateRoutes(Request $request)
    {
        if ($request->get('delete')) {
            $deleted = DB::delete('DELETE FROM routes');
            dd($deleted . ' registers has ben deleted!');;
        }

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $routes = DB::table(self::OLD_TABLES['routes'])->get();
        foreach ($routes as $routeOLD) {
            $new = false;
            $route = Route::find($routeOLD->id_rutas);
            if (!$route) {
                $route = new Route();
                $new = true;
            }
            $route->id = $routeOLD->id_rutas;
            $route->name = $routeOLD->nombre;
            $route->distance = $routeOLD->distancia;
            $route->road_time = $routeOLD->tiempo_recorrido;
            $route->company_id = $routeOLD->id_empresa;
            $route->dispatch_id = $routeOLD->id_despacho;
            $route->active = $routeOLD->estado == 0 ? true : false;

            $routeGoogle = RouteGoogle::find($route->id);
            $route->url = $routeGoogle ? $routeGoogle->url : "";

            try {
                $route->save();
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

    public function migrateUsers(Request $request)
    {
        if ($request->get('delete')) {
            $deleted = DB::delete('DELETE FROM users');
            dd($deleted . ' registers has ben deleted!');;
        }

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $users = DB::table(self::OLD_TABLES['users'])->get();
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
            $deleted = DB::delete('DELETE FROM gps_vehicles');
            dump($deleted . ' registers has ben deleted from gps_vehicles!');;
            $deleted = DB::delete('DELETE FROM vehicles');
            dd($deleted . ' registers has ben deleted!');;
        }
        /* For vehicles table */
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;

        /* For gps_vehicles table */
        $gpsVehicleTotalCreated = 0;
        $gpsVehicleTotalUpdated = 0;
        $gpsVehicleTotalErrors = 0;

        $vehicles = DB::table(self::OLD_TABLES['vehicles'])->get();
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
            $vehicle->active = $vehicleOLD->estado == 1 ? true : false;
            $vehicle->in_repair = $vehicleOLD->en_taller == 1 ? true : false;

            try {
                $vehicle->save();
                $new ? $totalCreated++ : $totalUpdated++;

                /* Migrate data for gps_vehicle */

                $gpsVehicleNew = false;
                $gpsVehicle = GpsVehicle::whereVehicleId($vehicleOLD->id_crear_vehiculo)->get()->first();
                if (!$gpsVehicle) {
                    $gpsVehicle = new GpsVehicle();
                    $gpsVehicleNew = true;
                }
                $gpsVehicle->imei = $vehicleOLD->imei_gps ?? $vehicleOLD->placa;
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
                dump($e->getMessage());
            } catch (\PDOException $e) {
                $totalErrors++;
                dump($e->getMessage());
            }
        }

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors,
            '------------------------------',
            'Gps Vehicle Total Created' => $gpsVehicleTotalCreated,
            'Gps Vehicle Total Updated' => $gpsVehicleTotalUpdated,
            'Gps Vehicle Total Errors' => $gpsVehicleTotalErrors,
        ]);
    }

    public function migrateControlPoints(Request $request)
    {
        if ($request->get('delete')) {
            $deleted = DB::delete('DELETE FROM control_points');
            dd($deleted . ' registers has ben deleted!');
        }

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        $controlPoints = DB::table(self::OLD_TABLES['control_points'])->whereIn('id_ruta', self::ROUTES_FOR_MIGRATE_CP)->get();
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
            $controlPoint->distance_from_dispatch = $controlPointOLD->distancia_desde_despacho;
            $controlPoint->distance_next_point = intval($controlPointOLD->distancia_punto_siguiente);
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

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateFringes(Request $request)
    {
        $new = true;
        DB::statement("TRUNCATE control_point_times");
        DB::statement("DELETE FROM fringes");
        DB::statement("SELECT pg_catalog.setval('fringes_id_seq', 1, false)");

        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;

        $fringes = DB::table(self::OLD_TABLES['fringes'])
            ->whereIn('id_ruta', self::ROUTES_FOR_MIGRATE)
            ->get();

        foreach ($fringes as $fringeOLD) {
            for ($i = 1; $i <= 15; $i++) {
                $fringeI = $fringeOLD->{"franja_$i"};
                if ($fringeI == "") break;

                $fringeTime = explode(" a ", $fringeI);

                $fringe = new Fringe();
                $fringe->name = $fringeI;
                $fringe->from = $fringeTime[0];
                $fringe->to = $fringeTime[1];
                $fringe->sequence = $i;
                $fringe->route_id = $fringeOLD->id_ruta;
                $fringe->day_type_id = $fringeOLD->tipo_de_dia;

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

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }

    public function migrateControlPointTimes(Request $request)
    {
        $new = true;
        DB::statement("TRUNCATE control_point_times");
        DB::statement("SELECT pg_catalog.setval('control_point_times_id_seq', 1, false)");
        DB::statement("UPDATE tiempos_punto_control SET tiempo1 = '00:00' WHERE tiempo1 = ''");
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalErrors = 0;
        //$controlPointTimes = DB::table(self::OLD_TABLES['control_point_times'])->where('id_ruta','=',126)->get();

        foreach (self::ROUTES_FOR_MIGRATE as $route) {
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

            foreach ($controlPointTimesByDays as $controlPointTimes){
                for ($i = 1; $i <= 15; $i++) {
                    $day_type = 0;
                    $last_time = "00:00:00";

                    foreach ($controlPointTimes as $index => $controlPointTimesOLD) {
                        if ($day_type != $controlPointTimesOLD->day_type) {
                            $day_type = $controlPointTimesOLD->day_type;
                            $last_time = "00:00:00";
                        }

                        $timeFringeI = $controlPointTimesOLD->{"time$i"};

                        $fringe = Fringe::where('route_id', $route)
                            ->where('day_type_id', $day_type)
                            ->where('sequence', $i)
                            ->get()->first();

                        if ($fringe && $timeFringeI != "") {
                            $controlPointTime = new ControlPointTime();
                            $controlPointTime->time = Database::parseIntervalToTime($timeFringeI);
                            $controlPointTime->time_next_point = Database::parseIntervalToTime(($index == (count($controlPointTimes) - 1)) ? $controlPointTime->time : $controlPointTimes[$index + 1]->{"time$i"});
                            $controlPointTime->time_from_dispatch = Database::parseIntervalToTime(date("H:i:s", strtotime($last_time) + strtotime($controlPointTime->time) - strtotime("00:00:00")));
                            $controlPointTime->day_type_id = $day_type;
                            $controlPointTime->control_point_id = $controlPointTimesOLD->control_point_id;
                            $controlPointTime->fringe_id = $fringe->id;

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

        dd([
            'Total Created' => $totalCreated,
            'Total Updated' => $totalUpdated,
            'Total Errors' => $totalErrors
        ]);
    }
}
