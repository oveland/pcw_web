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
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function map(Request $request)
    {

        $dr = DispatchRegister::find(328855);

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

        dd("TOTAL $total - ".$passengers->first()->total." = ".($passengers->last()->total - $passengers->first()->total) );return view('tools.map');
    }

    public function smartRecovery(Request $request)
    {
        echo "<style>.sf-dump{ margin: 0 !important; padding: 0 !important; }</style>";
        $company = Company::find($request->get('company'));
        $date = $request->get('date');

        dump("STARTING RECOVERY PROCESS FOR $company->short_name at $date");

        //$vehicles = $company->vehicles;
        $plate = $request->get('plate');
        $vehicle = Vehicle::where('plate',$plate)->get()->first();
        if($vehicle)$vehicles = collect([])->push($vehicle);
        else dd("Vehicle $plate not found");

        foreach ($vehicles as $vehicle) {
            $dispatchRegisters = DispatchRegister::completed()
                ->where('date', $date)
                ->where('vehicle_id', $vehicle->id)
                ->orderBy('departure_time', 'asc')
                ->get();

            if( $dispatchRegisters->isNotEmpty() ){
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

                            if( $speed < 120 ){
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
                                if($saved)$newLocations++;
                            }else{
                                if($timeInSeconds > 0)dump("    XXXX ($historicLocation->time): Inconsistent Speed $speed km/h: $distanceInMeters meters in $timeInSeconds seconds");
                            }
                            $lasHistoricLocation = $historicLocation;
                        }
                        dump("#$vehicle->number End recovery. Saved $newLocations new locations in $route->name round trip $dispatchRegister->round_trip");
                    }else{
                        dump("Vehicle $vehicle->number in $route->name round trip $dispatchRegister->round_trip not processed: historicLocations: ".$historicLocations->count());
                    }
                }
                if($processed)dd("");
                echo "<hr>";
            }
        }
        dd("PROCESS FINISHED");
    }

    function showScript($gps, Request $request){
        $fileName = "scripts/$gps.txt";
        if( !\File::exists(public_path($fileName)) )return view('errors.404');
        $scriptText = \File::get(public_path($fileName));
        $scriptText = trim($scriptText);
        return view('tools.scripts', compact(['scriptText','gps']));
    }

    function sendMailReports(){
        $dates = [
            '2019-01-21',
            '2019-01-22',
        ];

        foreach ($dates as $date){
            $exitCode = \Artisan::call('send-mail:consolidated', [
                '--date' => $date,
                '--prod' => true
            ]);

            dump("Send report via email for date $date => CODE: $exitCode");
        }

        dd('End process!!');
    }
}
