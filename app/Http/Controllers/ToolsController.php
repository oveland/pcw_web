<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Passengers\Passenger;
use App\Models\Routes\DispatchRegister;
use App\Http\Controllers\Utils\Geolocation;
use App\Models\Vehicles\Location;
use App\Mail\ConsolidatedReportMail;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Mail;
use Illuminate\Http\Request;

class ToolsController extends Controller
{

    public function checkGPSLimbo(Request $request){
        $on = $request->get('on');
        $on = $on ? $on : "04-marzo";

        $gpsLimbo = [
            "04-marzo" => "('7', '9', '10', '23', '24', '2000', '2038', '2066', '2070', '2182', '2342', '2346', '2347', '2349', '2406', '2420', '2427', '2430', '2448', '2477', '2484', '4402', '4455', '4456', '4466', '4483', '4484', '4486', '4492', '4516', '4559')",
            "05-marzo" => "('5', '11', '24', '1260', '2337', '2347', '2353', '2406', '2420', '2448', '2449', '2477', '4210', '4455', '4516', '4544', '4559')",
        ];

        $simGPSLimbo = \DB::select("
            SELECT sg.*, cl.date cl_date, (m.fecha||' '||m.hora) date, sv.*, m.status vehicle_status_id FROM sim_gps_excel as sg
            left join current_locations as cl on (cl.vehicle_id = sg.vehicle_id)
            left join markers as m on (m.name = sg.plate)  
            left join status_vehi as sv on (sv.id_status = m.status)  
            WHERE sg.number IN $gpsLimbo[$on] ORDER BY sg.hardware_name DESC, sg.number::INTEGER
        ");

        return view('tools.gpsLimbo', compact('simGPSLimbo'));
    }

    public function test(Request $request)
    {
        $dates = "
             1:42:06 AM 02/03/2019
11:15:22 AM 01/03/2019
11:41:38 AM 05/03/2019
 2:01:53 AM 02/03/2019
12:36:19 AM 02/03/2019
11:35:46 AM 05/03/2019
 1:02:54 AM 02/03/2019
12:57:28 AM 02/03/2019
10:32:49 PM 01/03/2019
11:34:25 AM 05/03/2019
11:46:04 AM 05/03/2019
 1:29:03 AM 02/03/2019
11:45:09 AM 05/03/2019
10:50:38 AM 05/03/2019
 1:38:35 AM 02/03/2019
11:24:54 AM 05/03/2019
 1:54:17 AM 02/03/2019
 6:50:54 AM 03/03/2019
 1:56:31 AM 02/03/2019
12:44:19 AM 02/03/2019
 1:35:22 AM 02/03/2019
10:05:39 AM 12/01/2019
 7:43:42 AM 22/01/2019
 4:54:52 PM 12/02/2019
11:45:40 AM 05/03/2019
 3:47:04 AM 14/02/2019
11:11:55 AM 05/03/2019
12:48:03 AM 02/03/2019
 1:15:42 AM 02/03/2019
 1:14:50 AM 02/03/2019
11:45:29 AM 05/03/2019
 1:52:23 AM 02/03/2019
12:42:44 AM 02/03/2019
11:44:17 AM 05/03/2019
 4:19:06 PM 09/11/2018
 1:15:34 AM 02/03/2019
10:52:27 PM 01/03/2019
11:32:30 PM 02/03/2019
11:45:04 AM 05/03/2019
 5:50:17 PM 27/02/2019
 8:54:15 AM 27/02/2019
12:33:58 AM 02/03/2019
 1:07:41 AM 02/03/2019
10:55:48 AM 05/03/2019
 1:14:18 AM 02/03/2019
11:44:54 AM 05/03/2019
10:42:10 AM 05/01/2019
 2:15:14 PM 04/03/2019
11:44:06 AM 05/03/2019
11:30:53 PM 02/03/2019
11:32:04 PM 02/03/2019
10:33:08 AM 05/03/2019
 1:06:49 AM 02/03/2019
 5:13:56 PM 09/11/2018
 2:41:04 PM 25/01/2019
11:44:06 AM 05/03/2019
11:22:33 PM 02/03/2019
12:40:53 AM 02/03/2019
 1:10:13 AM 02/03/2019
 2:05:55 AM 05/03/2019
 1:07:15 AM 02/03/2019
 1:31:42 AM 02/03/2019
 1:12:50 AM 02/03/2019
11:32:32 PM 02/03/2019
 8:18:07 PM 01/03/2019
11:46:00 AM 05/03/2019
11:32:40 PM 02/03/2019
11:44:45 AM 05/03/2019
 1:30:26 AM 02/03/2019
11:05:26 PM 02/03/2019
 1:55:43 AM 05/03/2019
 3:49:38 PM 01/03/2019
11:28:01 AM 05/03/2019
12:45:53 AM 02/03/2019
 3:43:53 PM 24/02/2019
11:15:12 AM 28/01/2019
 1:56:55 AM 05/03/2019
 7:23:26 AM 03/03/2019
11:04:42 AM 05/03/2019
11:31:15 PM 02/03/2019
11:45:42 AM 05/03/2019
12:48:13 AM 02/03/2019
 9:11:01 PM 01/03/2019
 9:37:25 PM 02/03/2019
 1:29:44 AM 02/03/2019
 7:02:08 PM 01/03/2019
 1:13:47 AM 02/03/2019
11:45:34 AM 05/03/2019
 9:44:41 AM 04/03/2019
 1:25:04 AM 02/03/2019
 6:10:45 PM 02/03/2019
11:45:27 AM 05/03/2019
11:45:58 AM 05/03/2019
 7:28:53 PM 04/03/2019
11:46:03 AM 05/03/2019
11:45:52 AM 05/03/2019
11:41:38 AM 05/03/2019
11:32:44 PM 02/03/2019
 1:15:22 AM 02/03/2019
11:44:23 AM 05/03/2019
11:16:49 PM 01/03/2019
12:38:11 AM 02/03/2019
 1:51:52 AM 05/03/2019
 4:56:23 PM 07/12/2018
11:30:56 PM 02/03/2019
 7:42:22 PM 04/03/2019
 1:27:51 AM 02/03/2019
12:40:56 AM 02/03/2019
 4:15:37 PM 29/11/2018
11:45:54 AM 05/03/2019
12:59:16 AM 02/03/2019
11:32:46 PM 02/03/2019
 1:29:32 AM 02/03/2019
11:32:25 PM 02/03/2019
11:43:03 AM 05/03/2019
11:45:41 AM 05/03/2019
12:52:26 AM 02/03/2019
 1:00:16 AM 02/03/2019
11:06:46 AM 05/03/2019
11:44:55 AM 05/03/2019
11:44:36 AM 05/03/2019
 9:59:02 AM 05/03/2019
 1:25:56 AM 02/03/2019
11:43:38 AM 05/03/2019
12:58:25 AM 02/03/2019
 1:07:02 AM 02/03/2019
12:34:00 AM 02/03/2019
12:59:19 AM 02/03/2019
 4:23:42 AM 05/03/2019
12:35:36 AM 02/03/2019
 1:07:52 AM 02/03/2019
11:31:02 PM 02/03/2019
 9:43:20 PM 01/03/2019
11:34:39 PM 02/03/2019
10:52:09 PM 02/03/2019
11:30:12 PM 02/03/2019
11:30:39 PM 02/03/2019
11:29:59 PM 02/03/2019
11:09:36 PM 02/03/2019
10:55:58 PM 02/03/2019
10:42:02 PM 02/03/2019
10:58:20 PM 02/03/2019
10:55:55 PM 02/03/2019
10:37:32 PM 02/03/2019
11:36:45 AM 05/03/2019
10:42:40 PM 02/03/2019
11:45:54 AM 05/03/2019
10:43:06 PM 02/03/2019
 3:25:07 AM 05/03/2019
10:33:12 PM 02/03/2019
11:43:11 AM 05/03/2019
10:48:59 PM 02/03/2019
11:04:04 PM 02/03/2019
11:41:42 AM 05/03/2019
11:43:26 AM 05/03/2019
11:45:41 AM 05/03/2019
11:14:00 PM 02/03/2019
11:45:01 AM 05/03/2019
11:26:50 PM 02/03/2019
 4:21:38 PM 01/03/2019
 6:31:11 PM 04/03/2019
11:44:42 AM 05/03/2019
11:43:08 AM 05/03/2019
11:42:35 AM 05/03/2019
11:42:01 AM 05/03/2019
11:45:49 AM 05/03/2019
12:21:19 PM 04/03/2019
 5:40:07 PM 04/03/2019
11:42:31 AM 05/03/2019
        ";

        $dates = explode("\n", $dates);

        foreach ($dates as $date){
            $date = trim($date);
            if($date){
                $dateArray = explode(" ", $date);
                if(count($dateArray) >= 3){
                    // $format = count($dateArray) == 3 ? "d/m/Y h:i:s A" : "d/m/Y  h:i:s A";
                    $format = count($dateArray) == 3 ? "h:i:s A d/m/Y" : "h:i:s A d/m/Y";
                    $dateFormat = Carbon::createFromFormat($format, $date);

                    echo($dateFormat->toDateTimeString()."<br>");
                }else{
                    echo("----<br>");
                }
            }
        }

        dd();



        /*$q = (object)[
            "plate" => $request->get("plate"),
            "date" => $request->get("date"),
            "from" => $request->get("from"),
            "to" => $request->get("to"),
            "sort" => $request->get("sort"),
        ];

        $historic = \DB::select("SELECT * FROM markers_historial where id_gps = '$q->plate' AND fecha = '$q->date' and hora BETWEEN '$q->from' AND '$q->to' ORDER BY hora $q->sort");

        foreach ($historic as $h){
            echo "$h->hora > $h->frame";
            echo "<br>";
        }

        echo "COMPLETE GPS DATA: <br>";
        dd($historic);*/
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function map(Request $request)
    {

        /*$dr = DispatchRegister::find(328855);

        dump("SEARCH ON $dr->departure_time, $dr->arrival_time");

        $passengers = \DB::table('contador_eventos')
            ->where('fecha', $dr->date)
            ->where('id_gps', $dr->vehicle->plate)
            ->whereBetween('hora', [$dr->departure_time, $dr->arrival_time])
            ->orderBy('id_cont_eventos')->get();


        $total = 0;
        $index = 1;
        foreach ($passengers as $passenger){
            echo ("<br>$index $passenger->hora $passenger->total > $total".(($passenger->total < $total)?" XXXXXXXXXXXXXXXXXXXXXXXXXX":" OK"));
            $total = $passenger->total;
            $index++;
        }

        dd("TOTAL $total - ".$passengers->first()->total." = ".($passengers->last()->total - $passengers->first()->total) );*/

        return view('tools.map');
    }

    public function smartRecovery(Request $request)
    {
        echo "<style>.sf-dump{ margin: 0 !important; padding: 0 !important; }</style>";
        $company = Company::find($request->get('company'));
        $date = $request->get('date');

        dump("STARTING RECOVERY PROCESS FOR $company->short_name at $date");

        //$vehicles = $company->vehicles;
        $plate = $request->get('plate');
        $vehicle = Vehicle::where('plate', $plate)->get()->first();
        if ($vehicle) $vehicles = collect([])->push($vehicle);
        else dd("Vehicle $plate not found");

        foreach ($vehicles as $vehicle) {
            $dispatchRegisters = DispatchRegister::completed()
                ->where('date', $date)
                ->where('vehicle_id', $vehicle->id)
                ->orderBy('departure_time', 'asc')
                ->get();

            if ($dispatchRegisters->isNotEmpty()) {
                $processed = false;
                dump("Process for vehicle $vehicle->number ($vehicle->plate):");
                dump("---------------------------------");
                foreach ($dispatchRegisters as $dispatchRegister) {
                    $route = $dispatchRegister->route;
                    $locations = $dispatchRegister->locations;

                    $startTimeRange = $locations->isNotEmpty() ? $locations->last()->date->toTimeString() : $dispatchRegister->departure_time;
                    $endTimeRange = $dispatchRegister->arrival_time;

                    $historicLocations = collect(\DB::select("
                    SELECT TO_CHAR((fecha||' '||hora)::TIMESTAMP, 'DD/MM/YYYY HH24:MI:SS') date, hora \"time\", lat latitude, lng longitude, orientacion orientation, estado status 
                    FROM markers_historial 
                    WHERE fecha = '$date' 
                    AND id_gps = '$vehicle->plate' 
                    AND hora BETWEEN '$startTimeRange' AND '$endTimeRange' 
                    ORDER BY hora ASC
                "));

                    $dataToRecovery = (object)[
                        "dispatch_register_id " => $dispatchRegister->id,
                        "info_dispatch " => "Vehicle $vehicle->number ($vehicle->id) $route->name round trip $dispatchRegister->round_trip > $dispatchRegister->departure_time to $dispatchRegister->arrival_time",
                        "locationsSize" => $locations->count(),
                        'historic_range' => "$startTimeRange to $endTimeRange",
                        'historicLocationsSize' => $historicLocations->count(),
                        'firstHistoricLocation' => $historicLocations->first()
                    ];

                    if ($historicLocations->count() > 50) {
                        $processed = true;
                        dump($dataToRecovery);
                        $lastLocation = Location::where('vehicle_id', $vehicle->id)
                            ->where('date', '<=', $historicLocations->first()->date)
                            ->orderBy('date', 'desc')
                            ->limit(1)
                            ->get()
                            ->first();

                        //dump("#$vehicle->number ($vehicle->id) Last Location: $lastLocation->date ($lastLocation->latitude, $lastLocation->longitude) Odometer $lastLocation->odometer");

                        $odometer = $lastLocation->odometer;
                        $mileage = $lastLocation->distance;
                        $lasHistoricLocation = $historicLocations->first();

                        $newLocations = 0;

                        foreach ($historicLocations as $historicLocation) {
                            $distanceInMeters = intval(Geolocation::getDistance($historicLocation->latitude, $historicLocation->longitude, $lasHistoricLocation->latitude, $lasHistoricLocation->longitude));
                            $timeInSeconds = Carbon::createFromFormat(config('app.simple_date_time_format'), $historicLocation->date)->diffInSeconds(Carbon::createFromFormat(config('app.simple_date_time_format'), $lasHistoricLocation->date));

                            $speed = $timeInSeconds > 0 ? ($distanceInMeters * 3600) / ($timeInSeconds * 1000) : 120;

                            if ($speed < 120) {
                                $odometer += $distanceInMeters;
                                $mileage += $distanceInMeters;

                                $location = new Location();
                                $location->id = \DB::select("SELECT nextval('locations_id_seq') id")[0]->id;
                                $location->version = 0;
                                $location->vehicle_id = $vehicle->id;
                                $location->date = $historicLocation->date;
                                $location->latitude = $historicLocation->latitude;
                                $location->longitude = $historicLocation->longitude;
                                $location->orientation = $historicLocation->orientation;
                                $location->odometer = $odometer;
                                $location->status = 'A';
                                $location->speed = $speed;
                                $location->speeding = $speed > 60;
                                $location->vehicle_status_id = $historicLocation->status;
                                $location->distance = $mileage;

                                $location->dispatch_register_id = $dispatchRegister->id;
                                $location->off_road = false;

                                $saved = $location->save();
                                //dump(" > $historicLocation->time | $distanceInMeters meters in $timeInSeconds s. (".intval($speed)." Km/h). NEW Odometer $odometer ".($saved?"Saved OK":"NOT SAVED!!!!"));
                                if ($saved) $newLocations++;
                            } else {
                                if ($timeInSeconds > 0) dump("    XXXX ($historicLocation->time): Inconsistent Speed $speed km/h: $distanceInMeters meters in $timeInSeconds seconds");
                            }
                            $lasHistoricLocation = $historicLocation;
                        }
                        dump("#$vehicle->number End recovery. Saved $newLocations new locations in $route->name round trip $dispatchRegister->round_trip");
                    } else {
                        dump("Vehicle $vehicle->number in $route->name round trip $dispatchRegister->round_trip not processed: historicLocations: " . $historicLocations->count());
                    }
                }
                if ($processed) dd("");
                echo "<hr>";
            }
        }
        dd("PROCESS FINISHED");
    }

    function showScript($gps, Request $request)
    {
        $fileName = "scripts/$gps.txt";
        if (!\File::exists(public_path($fileName))) return view('errors.404');
        $scriptText = \File::get(public_path($fileName));
        $scriptText = trim($scriptText);
        return view('tools.scripts', compact(['scriptText', 'gps']));
    }

    function sendMailReports(Request $request)
    {

        switch ($request->get('type')) {
            case 'routes':

                $dates = [
                    '2019-03-21',
                    '2019-03-22',
                    '2019-03-23',
                    '2019-03-24',
                    '2019-03-25',
                    '2019-03-26',
                    '2019-03-27',
                    '2019-03-28',
                    '2019-03-29',
                    '2019-03-30',
                    '2019-03-31'
                ];

                foreach ($dates as $date) {
                    $exitCode = \Artisan::call('send-mail:consolidated', [
                        '--date' => $date,
                        '--prod' => true
                    ]);

                    dump("Send report via email for date $date => CODE: $exitCode");
                }

                dd('End process!!');

                break;
            case 'passengers':

                $dates = [
                    '2019-03-21',
                    '2019-03-22',
                    '2019-03-23',
                    '2019-03-24',
                    '2019-03-25',
                    '2019-03-26',
                    '2019-03-27',
                    '2019-03-28',
                    '2019-03-29',
                    '2019-03-30',
                    '2019-03-31'
                ];

                foreach ($dates as $date) {
                    $exitCode = \Artisan::call('send-mail:consolidated-passengers', [
                        '--date' => $date,
                        '--prod' => true
                    ]);

                    dump("Send passengers report via email for date $date => CODE: $exitCode");
                }

                dd('End process!!');

                break;
            default:
                dd('No type report found!');
                break;
        }
    }
}
