<?php

//NUEVO:

namespace App\Services\API\Web\Track;


use App\Models\Users\User;
use Auth;
use Exception;
use RestClient\Client;

class TrackMapService
{
    /**
     * @param $companyId
     * @param $routeID
     * @return string
     */
    function buildSQLQuery($companyId, $routeID)
    {
        //$user = Auth::user();
        $user = User::find(625565);
        $companyId = $companyId ? $companyId : 0;
        $nivel = $user->role_id;

        $mainQuery = "";

        if ($user->isAdmin()) {
            $mainQuery = "SELECT DISTINCT 
            m.id,
            m.name,	
            m.lat,
            m.lng,	
            v.id_crear_vehiculo, 
            v.placa,
            v.num_vehiculo,	
            CASE WHEN ( SELECT count(mv) FROM maintenance_vehicles mv WHERE mv.vehicle_id = v.id_crear_vehiculo AND mv.date = current_date) > 0 THEN
                TRUE
            ELSE
                FALSE
            END in_maintenance,
            CASE WHEN v.en_taller = 1 THEN
                TRUE
            ELSE
                FALSE
            END in_repair,	
            v.observaciones observaciones,
            c.pas_tot,
            c.des_p1,
            c.frame,
            c.observations observations_counter,
            c.hora hora_trama_contador,
            c.total,
            c.intermunicipal,
            c.hora_status hora_contador,
            c.time_change_sensor_recorder,	
            m.hora_status,
            v.empresa,
            sv.id_status status,
            sv.des_status,
            sv.main_class status_main_class,	
            sv.icon_class status_icon_class,	
            m.hora,	
            m.orientacion,
            m.fecha,
            count(csr.id) AS excesos_velocidad,		
            cdr.route_id,
            cdr.route_name,
            cdr.round_trip,
            cdr.departure_time,
            cdr.arrival_time,
            cdr.dispatch_register_id,
            dv_group.route_id dv_route_id,
            r.name dv_route_name,	
            cof.alert_off_road,
            cl.id current_location_id,
            cl.speed,
            cl.current_mileage,	
            cr.id current_report_id,
            cr.distancem,
            cr.distancep,
            cr.timem,
            cr.timep,
            cr.timed,
            cr.status status_current_report,
            cpr.id alert_parked,
            ccpa.id alert_control_point,
            ccpa.trajectory control_point_alert_trajectory 
            FROM
            markers as m
            INNER JOIN crear_vehiculo as v ON (v.placa = m.name AND v.estado = '1')
            INNER JOIN contador as c ON (c.placa = m.name)	
            INNER JOIN status_vehi as sv ON (sv.id_status = m.status)
            LEFT JOIN current_dispatch_registers AS cdr ON (cdr.plate = m.name)
            LEFT JOIN dispatcher_vehicles AS dv_group ON (dv_group.vehicle_id = v.id_crear_vehiculo ".($routeID ? 'AND': 'OR')." dv_group.route_id = $routeID)
            LEFT JOIN dispatcher_vehicles AS dv ON (dv.vehicle_id = dv_group.vehicle_id AND dv.\"default\" is true)
            LEFT JOIN routes AS r ON (r.id = dv.route_id)
            LEFT JOIN current_off_roads AS cof ON (cof.dispatch_register_id = cdr.dispatch_register_id AND cof.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_reports AS cr ON (cr.vehicle_id = v.id_crear_vehiculo AND cr.dispatch_register_id = cdr.dispatch_register_id)
            LEFT JOIN current_locations AS cl ON (cl.vehicle_id = v.id_crear_vehiculo)
            LEFT JOIN current_parking_reports AS cpr ON (cpr.vehicle_id = v.id_crear_vehiculo AND cpr.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_control_point_alerts AS ccpa ON (ccpa.vehicle_id = v.id_crear_vehiculo AND ccpa.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_speeding_reports  as csr ON (csr.date = current_date AND csr.vehicle_id = v.id_crear_vehiculo AND csr.time > (current_time-TO_TIMESTAMP('00:00:20', 'HH24:MI:SS')::TIME) AND csr.time <= current_time)
            WHERE (v.empresa != 21 OR $routeID = 0 OR r.id = $routeID OR dv_group.route_id = $routeID) ";
        } elseif ($nivel == 1 or $nivel == 2 or $nivel == 4) {
            $companyId = $user->company->id;
            $mainQuery = "
            SELECT DISTINCT  
            m.id,
            m.name,	
            m.lat,
            m.lng,	
            v.id_crear_vehiculo,
            v.placa,
            v.num_vehiculo,	
            CASE WHEN ( SELECT count(mv) FROM maintenance_vehicles mv WHERE mv.vehicle_id = v.id_crear_vehiculo AND mv.date = current_date) > 0 THEN
                TRUE
            ELSE
                FALSE
            END in_maintenance,
            CASE WHEN v.en_taller = 1 THEN
                TRUE
            ELSE
                FALSE
            END in_repair,	
            v.observaciones observaciones,
            c.pas_tot,
            c.des_p1,
            c.total,
            c.intermunicipal,
            c.frame,
            c.observations observations_counter,
            c.hora hora_trama_contador,
            c.hora_status hora_contador,
            c.time_change_sensor_recorder,	
            m.hora_status,
            v.empresa,
            sv.id_status status,
            sv.des_status,
            sv.main_class status_main_class,	
            sv.icon_class status_icon_class,
            m.hora,	
            m.orientacion,
            m.fecha,
            count(csr) as excesos_velocidad,
            cdr.route_id,
            cdr.route_name,
            cdr.round_trip,
            cdr.departure_time,
            cdr.arrival_time,
            cdr.dispatch_register_id,
            dv_group.route_id dv_route_id,
            r.name dv_route_name,
            cof.id alert_off_road,
            cl.id current_location_id,
            cl.speed,
            cl.current_mileage,
            cr.id current_report_id,
            cr.distancem,
            cr.distancep,
            cr.timem,
            cr.timep,
            cr.timed,
            cr.status status_current_report,
            cpr.id alert_parked,
            ccpa.id alert_control_point,
            ccpa.trajectory control_point_alert_trajectory
            FROM
            markers as m	
            INNER JOIN crear_vehiculo as v ON (v.placa = m.name AND v.estado = '1')
            INNER JOIN contador as c ON (c.placa = m.name)	
            INNER JOIN status_vehi as sv ON (sv.id_status = m.status)
            LEFT JOIN current_dispatch_registers as cdr ON (cdr.plate = m.name)
            LEFT JOIN dispatcher_vehicles AS dv_group ON (dv_group.vehicle_id = v.id_crear_vehiculo ".($routeID ? 'AND': 'OR')." dv_group.route_id = $routeID)
            LEFT JOIN dispatcher_vehicles AS dv ON (dv.vehicle_id = dv_group.vehicle_id AND dv.\"default\" is true)
            LEFT JOIN routes AS r ON (r.id = dv.route_id)
            LEFT JOIN current_off_roads as cof ON (cof.dispatch_register_id = cdr.dispatch_register_id AND cof.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_reports AS cr ON (cr.vehicle_id = v.id_crear_vehiculo AND cr.dispatch_register_id = cdr.dispatch_register_id)
            LEFT JOIN current_locations AS cl ON (cl.vehicle_id = v.id_crear_vehiculo)
            LEFT JOIN current_parking_reports as cpr ON (cpr.vehicle_id = v.id_crear_vehiculo AND cpr.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_control_point_alerts AS ccpa ON (ccpa.vehicle_id = v.id_crear_vehiculo AND ccpa.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)	
            LEFT JOIN current_speeding_reports  as csr ON (csr.date = current_date AND csr.vehicle_id = v.id_crear_vehiculo AND csr.time > (current_time-TO_TIMESTAMP('00:00:20', 'HH24:MI:SS')::TIME) AND csr.time <= current_time)
            WHERE (v.empresa != 21 OR $routeID = 0  OR r.id = $routeID OR dv_group.route_id = $routeID) ";
        } elseif ($nivel == 3) {
            $companyId = $user->company->id;
            $mainQuery = "
            SELECT DISTINCT
            m.id,
            m.name,	
            m.lat,
            m.lng,	
            v.id_crear_vehiculo, 
            v.placa,
            v.num_vehiculo,	
            CASE WHEN ( SELECT count(mv) FROM maintenance_vehicles mv WHERE mv.vehicle_id = v.id_crear_vehiculo AND mv.date = current_date) > 0 THEN
                TRUE
            ELSE
                FALSE
            END in_maintenance,
            CASE WHEN v.en_taller = 1 THEN
                TRUE
            ELSE
                FALSE
            END in_repair,	
            v.observaciones observaciones,
            c.pas_tot,	
            c.des_p1,
            c.total,
            c.intermunicipal,
            c.frame,
            c.observations observations_counter,
            c.hora hora_trama_contador,
            c.hora_status hora_contador,
            c.time_change_sensor_recorder,	
            m.hora_status,
            v.empresa,
            sv.des_status,
            sv.id_status status,
            sv.main_class status_main_class,	
            sv.icon_class status_icon_class,
            m.hora,	
            m.orientacion,
            m.fecha,
            count(csr) as excesos_velocidad,
            cdr.route_id,
            cdr.route_name,
            cdr.round_trip,
            cdr.departure_time,
            cdr.arrival_time,
            cdr.dispatch_register_id,
            dv_group.route_id dv_route_id,
            r.name dv_route_name,
            cof.id alert_off_road,
            cl.id current_location_id,
            cl.speed,
            cl.current_mileage,
            cr.id current_report_id,
            cr.distancem,
            cr.distancep,
            cr.timem,
            cr.timep,
            cr.timed,
            cr.status status_current_report,
            cpr.id alert_parked,
            ccpa.id alert_control_point,
            ccpa.trajectory control_point_alert_trajectory
            FROM
            markers as m
            INNER JOIN crear_vehiculo as v ON (v.placa = m.name AND v.estado = '1')
            INNER JOIN contador as c ON (c.placa = m.name)	
            INNER JOIN usuario_vehi ON (usuario_vehi.placa = m.name)	
            INNER JOIN status_vehi as sv ON (sv.id_status = m.status)
            INNER JOIN acceso ON (acceso.usuario = usuario_vehi.usuario)
            LEFT JOIN current_dispatch_registers as cdr ON (cdr.plate = m.name)
            LEFT JOIN dispatcher_vehicles AS dv_group ON (dv_group.vehicle_id = v.id_crear_vehiculo ".($routeID ? 'AND': 'OR')." dv_group.route_id = $routeID)
            LEFT JOIN dispatcher_vehicles AS dv ON (dv.vehicle_id = dv_group.vehicle_id AND dv.\"default\" is true)
            LEFT JOIN routes AS r ON (r.id = dv.route_id)
            LEFT JOIN current_off_roads as cof ON (cof.dispatch_register_id = cdr.dispatch_register_id AND cof.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_reports AS cr ON (cr.vehicle_id = v.id_crear_vehiculo AND cr.dispatch_register_id = cdr.dispatch_register_id)
            LEFT JOIN current_locations AS cl ON (cl.vehicle_id = v.id_crear_vehiculo)
            LEFT JOIN current_parking_reports as cpr ON (cpr.vehicle_id = v.id_crear_vehiculo AND cpr.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_control_point_alerts AS ccpa ON (ccpa.vehicle_id = v.id_crear_vehiculo AND ccpa.date >= (current_timestamp - '00:00:20'::TIME)::TIMESTAMP)
            LEFT JOIN current_speeding_reports  as csr ON (csr.date = current_date AND csr.vehicle_id = v.id_crear_vehiculo AND csr.time > (current_time-TO_TIMESTAMP('00:00:20', 'HH24:MI:SS')::TIME) AND csr.time <= current_time)
            WHERE " . " acceso.usuario =  '$user->username' and acceso.nivel = $nivel OR $routeID = 0 ";
        }

        $mainQuery .= ($companyId != 0) ? " AND (v.empresa = $companyId" . ($companyId == 12 ? " OR v.empresa = 25" : "") . ") " : "";

        $mainQuery .= " GROUP BY (
            m.id, 
            v.id_crear_vehiculo,
            c.id_contador, 
            sv.id_status, 
            sv.des_status, 
            sv.main_class,	
            sv.icon_class,    
            cdr.route_id,
            cdr.route_name,
            cdr.round_trip,
            cdr.departure_time,
            cdr.arrival_time,
            cdr.dispatch_register_id,
            dv.route_id,
            dv_group.route_id,
            r.name,
            cof.id,
            cr.id,    
            cl.id,
            cpr.id,
            ccpa.id
        )";

        return $mainQuery;
    }

    /**
     * @param $companyId
     * @return \Illuminate\Support\Collection|\Tightenco\Collect\Support\Collection
     */
    function getPassengerReport($companyId)
    {
        $apiHost = '';
        $passengersReport = collect();
        try {
            if ($companyId == 14) {
                $client = new Client($apiHost);
                $request = $client->newRequest("v1/passengers/report?company=$companyId");
                $res = json_decode($request->getResponse()->getParsedResponse());
                if (!$res->error) $passengersReport->push($res->passengersReport);
            }
        } catch (Exception $exception) {
        }
        $passengersReport = $passengersReport->first();

        return collect($passengersReport ? $passengersReport->reports : []);
    }

    /**
     * @param $companyId
     * @param $routeID
     * @return array
     */
    function track($companyId, $routeID)
    {
        $response = array();
        $passengersReport = $this->getPassengerReport($companyId);
        $rows = \DB::select($this->buildSQLQuery($companyId, $routeID));
        foreach ($rows as $row) {
            $vehiclesCurrentPeakAndPlate = [];

            $pass_tc = 0;
            $pass_tc_by_km = 0;
            $time_last_count_tc = $row->hora_contador;
            if ($row->intermunicipal == 't') {
                $row_tc = \DB::select("SELECT count(s) total_passengers, max(s.active_time)::TIME time_last_count, sum(s.busy_km)/1000 km_passenger,
                        (sum(s.busy_km)/(SELECT 1000*distancia::INTEGER FROM ruta WHERE id_rutas = 158))::DOUBLE PRECISION total_passengers_by_km
                    FROM summary_seats as s WHERE busy_km > 20000 AND plate = '$row->name'
                ");

                $row_tc = collect($row_tc)->first();

                if ($row_tc) {
                    $pass_tc = $row_tc->total_passengers;
                    $pass_tc_by_km = $row_tc->total_passengers_by_km;
                    $pass_tc_by_km = $pass_tc_by_km ? $pass_tc_by_km : 0;
                    $time_last_count_tc = $row_tc->time_last_count;
                }
            }

            $timeCounter = $time_last_count_tc ? $time_last_count_tc : $row->hora_contador;
            $frameCounter = $this->showFrameCounter($row->name) ? $row->frame : '';
            if ($row->name == 'VCK-531') $frameCounter = $this->addObsToFrame($frameCounter, $row->observations_counter);
            $totalPassengers = $row->intermunicipal == 't' ? $pass_tc : $row->pas_tot;

            $passengersByVehicle = null;

            if ($passengersReport->isNotEmpty()) {
                $passengersByVehicle = $passengersReport->where('vehicle_id', $row->id_crear_vehiculo)->first();
            }

            $speed = $row->speed;
            $speeding = 0;
            if ($companyId == 21) {
                if ($speed > 110) $speeding = 2;
            } else {
                $speed = $speed > 150 ? 150 : $speed;
                if ($speed > 60) $speeding = 2;
            }

            if ($row->lat == 0 or $row->lng == 0) {
                $row->lat = 3.466741247338114;
                $row->lng = -76.52156849484516;
            }

            $response[] = (object)[
                /* Info company */
                'companyId' => $companyId,

                /* Info vehicle */
                'vehiclePlate' => $row->name,
                'vehicleNumber' => $row->num_vehiculo,
                'vehicleStatusId' => $row->in_maintenance == 't' || $row->in_repair == 't' ? 31 : $row->status,
                'vehicleTimeStatus' => $row->hora_status,
                'vehicleWithPeakAndPlate' => (in_array($row->name, $vehiclesCurrentPeakAndPlate) ? true : false),
                'vehicleStatusName' => $row->des_status,
                'vehicleStatusMainClass' => $row->status_main_class,
                'vehicleStatusIconClass' => $row->status_icon_class,
                'vehicleIsIntermunicipal' => $row->intermunicipal,

                /* Info location */
                'time' => $row->hora,
                'date' => $row->fecha,
                'lat' => $row->lat,
                'lng' => $row->lng,
                'orientation' => $row->orientacion,
                'mileage' => $row->current_mileage,
                'speed' => $speed,
                'speeding' => $speeding,
                'observations' => $row->in_maintenance == 't' ? 'En mantenimiento programado' : $row->observaciones,
                'alertOffRoad' => $row->alert_off_road == 't' ? true : false,
                'alertParked' => $row->alert_parked == 't' ? true : false,
                'alertControlPoint' => $row->alert_control_point ? true : false,
                'controlPointAlertName' => 'COOPERATIVA',
                'controlPointAlertTrajectoryId' => $row->control_point_alert_trajectory,
                'controlPointAlertTrajectoryName' => $row->control_point_alert_trajectory == 0 ? 'IDA' : 'REGRESO',

                /* Info passengers */
                'passengers' => $passengersByVehicle ? $passengersByVehicle->passengers->sensor : $totalPassengers,             // Passengers by sensor
                'passengersByRecorder' => $passengersByVehicle ? $passengersByVehicle->passengers->recorder : 0,                // Passengers by manual recorder
                'passengersBySensorRecorder' => $passengersByVehicle ? $passengersByVehicle->passengers->sensorRecorder : 0,    // Passengers by sensor recorder
                'timeCounter' => ($passengersByVehicle ? $passengersByVehicle->passengers->timeSensor : $timeCounter) ?? '',
                'timePassengersByRecorder' => $passengersByVehicle ? $passengersByVehicle->passengers->timeRecorder : '--:--:--',
                'timeChangeSensorRecorder' => $passengersByVehicle ? $passengersByVehicle->passengers->timeSensorRecorder : '--:--:--',
                'passengersByKm' => $row->intermunicipal == 't' ? $pass_tc_by_km : 0,
                'frame' => ($passengersByVehicle ? $passengersByVehicle->passengers->dateSensor : '--:--:--') . " Pasajeros: $totalPassengers &emsp; Trama: $frameCounter",
                'timeFrameCounter' => explode(".", $row->hora_trama_contador)[0],
                'seatingTemplate' => $this->makeTemplateGetSeatStatus($row->name),


                /* Info route and route report */
                'dispatchRegisterRouteId' => $row->route_id ?? ($row->dv_route_id ?? 0),
                'dispatchRegisterRouteName' => $row->route_name ?? ($row->dv_route_name ?? 'SIN RUTA ASIGNADA'),
                'dispatchRegisterRoundTrip' => $row->round_trip ?? ($row->dv_route_id ? 'EN RUTA PREDEFINIDA' : 0),
                'reportStatusTimeDifference' => $row->timed,
                'reportStatusVehicle' => $row->status_current_report,
                'routeIdGroup' => $row->dv_route_id,

                'showAlerts' => true,
            ];
        }

        return $response;
    }

    function makeTemplateGetSeatStatus($plate)
    {
        /*
             * LIST OF PLATES FOR ACTIVE SEATS
        */
        $VehiclesWithSeatsSensor = [
            'EVD-544',  // 111      COOP
            'MBI-711',  // 15       COOP
            'FAE-838',  // 92       COOP
            'ZNK-927',  // 4062     TXC
            'ZNK-929',  // 4064     TXC
            'CBO-885',  // 885      PCW
            'PLA-001',  // 1      PCW
            //'PLA-002',  // 2      PCW
            'VJB-336',  // 15       TAX EMPERADOR
        ];

        if (!in_array($plate, $VehiclesWithSeatsSensor)) return "";
        $id = uniqid();
        $template_content = "<div id='$id' class='seating-container text-center' style='width: 190px !important;'>
        <button class='btn btn-default btn-xs btn-option btn-search-seating-status' data-plate='$plate' onclick='getSeatingStatus(this);'>
             <i class='fa fa-users'></i> Actualizar <i class='fa fa-refresh'></i>
        </button>
        <div class='col-md-12 col-xs-12 col-sm-12 detail no-padding'></div>
    </div>";

        return $template_content;
    }

    function showFrameCounter($plate)
    {
        /*
         * LIST OF PLATES FOR ACTIVE SEATS
        */
        $VehiclesShowFrameCounter = [
            'VCH-351',  // 356      ALAMEDA
            'VCK-531',  // 361      ALAMEDA
            'VCK-542',  // 338      ALAMEDA
            'VCD-672',  // 320      ALAMEDA
            'MBI-711',  // 15       COOP
            'FAE-838',  // 92       COOP
            'CBO-885',  // 885      PCW
            'PLA-001',  // 1        PCW
            'PLA-002',  // 2        PCW
            'VPF-180',  // 1146     MONTEBELLO
            'VJB-336',  // 177      TAX EMPERADOR
            'M-1614',   // 3        URECOOTRACO,
            "ZNK-929",  //  4064    TAX CENTRAL
            "ZNK-927",  //  4062    TAX CENTRAL
            "SAP-512",  // 001      TRANSPUBENZA
        ];

        return in_array($plate, $VehiclesShowFrameCounter);
    }

    /**
     * @param $frameCounter
     * @param $observationsCounter
     * @return string
     */
    function addObsToFrame($frameCounter, $observationsCounter)
    {
        $observationsCounter = json_decode($observationsCounter, false);
        $observationsCounterDisplay = "Ascensos: $observationsCounter->passengersOnBoard<br>";
        $observationsCounterDisplay .= "Descensos: $observationsCounter->passengersGettingOff<br>";
        $observationsCounterDisplay .= "Total Ascensos: $observationsCounter->totalPassengersOnBoard<br>";
        $observationsCounterDisplay .= "Total Descensos: $observationsCounter->totalPassengersGettingOff<br>";
        $observationsCounterDisplay .= "Pasajeros actuales en bus: $observationsCounter->currentPassengersOnBoard<br>";
        $observationsCounterDisplay .= "Calculado » $observationsCounter->totalPassengers";
        return "$frameCounter<br>--------------------------------<br>$observationsCounterDisplay</span><br>--------------------------------";
    }
}