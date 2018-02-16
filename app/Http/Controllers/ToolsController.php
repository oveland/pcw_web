<?php

namespace App\Http\Controllers;

use App\HistoryMarker;
use App\Http\Controllers\Utils\Geolocation;
use Carbon\Carbon;

class ToolsController extends Controller
{
    public function map()
    {
        /*$url = "https://roads.googleapis.com/v1/snapToRoads?path=-35.27801,149.12958|-35.28032,149.12907|-35.28099,149.12929|-35.28144,149.12984|-35.28194,149.13003|-35.28282,149.12956|-35.28302,149.12881|-35.28473,149.12836&interpolate=true&key=AIzaSyCNNGhrjaS4zQFwWVfazS2600h9hV-QpSA";

        $markers = HistoryMarker::limit(10)->get();
        $coordinatesGPS = array();
        $path = "";
        foreach ($markers as $marker) {
            $coordinatesGPS[] = collect($marker)->only(['lat', 'lng'])->toArray();
            $path .= $marker->lat . ',' . $marker->lng . '|';
        }
        $path = rtrim($path, '|');
        $coordinates = $this->googleSnapToRoad($path);
        dd($coordinates);*/


        // FOR CALCULATE COUNTER BY SENSOR FROM FRAME COUNTER
        /*$date = '2018-02-09';
        $counts = \DB::select("
            SELECT *
            FROM contador_eventos
            WHERE fecha = '$date' AND id_gps = 'M-1614'
            ORDER BY hora ASC
        ");

        $updated = 0;
        $lastTotal = 0;
        $totalFromTorres = 0;
        foreach ($counts as $count){
            try{
                $totalFromTorres = self::getAverageFromTORRESCriterion($count->frame);
                $totalFromTorres = ($totalFromTorres>$lastTotal)?$totalFromTorres:$lastTotal;
                $lastTotal = $totalFromTorres;
                $new = \DB::update("UPDATE contador_eventos SET total=$totalFromTorres WHERE id_cont_eventos=$count->id_cont_eventos");
                if($new){
                    $updated++;
                }else{
                    dump("NO SE ACTUALIZÓ, $new");
                }
            }catch (\Exception $e){
                dump($e->getMessage());
            }
        }

        dump("**********************************************************************");
        
        dump("** DATE: ".$date);
        dump("** TOTAL ROWS: ".count($counts));
        dump("** TOTAL UPDATED: $updated");
        dd("****** TOTAL PASS FINAL: $totalFromTorres");
        */
        return view('tools.map');
    }

    public static function getAverageFromTORRESCriterion($frame)
    {
        $f = collect(explode(" ","0 $frame"))->map(function($item,$key){
            return abs(intval($item));
        });

        $i = collect([]);
        $i->push(0);
        $i->push(($f[2] + $f[3]) / 2);                          // 1°
        $i->push($f[4] + $f[5] - $f[6] - $f[7] - $f[8]);        // 2°
        $i->push($f[7] + $f[8] + $f[9]);                        // 3°
        $i->push($f[10] + $f[11] - $f[12] - $f[13] - $f[14]);   // 4°
        $i->push($f[13] + $f[14] + $f[15]);                     // 5°
        $i->push($f[16] + $f[17] + $f[18]);                     // 6°
        $i->push($f[19] + $f[20] + $f[21]);                     // 7°
        $i->push(($f[22] + $f[23]) / 2);                        // 8°
        $i->push(($f[24] + $f[25]) / 2);                        // 9°

        $greater1 = collect([$f[2], $i->get(2), $i->get(3), $i->get(6), $i->get(8)])->max();
        $greater2 = collect([$f[3], $i->get(4), $i->get(5), $i->get(7), $i->get(9)])->max();
        $averageGreater = ($greater1 + $greater2) / 2;

        $Base1 = $f[2];
        $Base2 = $f[3];
        $NSIT1 = $i->get(3);
        $NSIT2 = $i->get(5);
        $TS1 = $i->get(2);
        $TS2 = $i->get(4);
        $TI1 = $i->get(6);
        $TI2 = $i->get(7);

        $Error1 = ($Base1 - $NSIT1) + ($Base2 - $NSIT2);
        $Error2 = ($NSIT1 - $TS1) + ($NSIT2 - $TS2);
        $Error3 = ($Base1 - $TS1) + ($Base2 - $TS2);
        $Error4 = ($Base1 - $TI1) + ($Base2 - $TI2);

        return intval($averageGreater + $Error1 + $Error2 - $Error3 - $Error4);
    }

    /**
     * This estimates the route from path coordinates reported by GPS
     *
     * @param $path
     * @return array
     */
    public function googleSnapToRoad($path)
    {
        $key = config('road.google_api_token');
        $url = 'https://roads.googleapis.com/v1/snapToRoads?path=' . $path . '&interpolate=true&key=' . $key . ' ';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $data = curl_exec($curl);
        curl_close($curl);

        $coordinates = array();
        if ($data) {
            $json = json_decode($data, true);
            $points = $json['snappedPoints'];
            foreach ($points as $key => $value) {
                $obj = (object)$value;
                $coordinates[] = (object)$obj->location;
            }
        }
        return $coordinates;
    }

    public function getRouteDistance(Route $route)
    {
        $coordinates = RouteReportController::getRouteCoordinates($route->url);
        $content = "";
        foreach ($coordinates as $coordinate) {
            $coordinate = (object)$coordinate;
            $content .= "$coordinate->latitude, $coordinate->longitude\n";
        }

        return $content;
    }

    public function getRouteDistanceFromUrl($url)
    {
        $coordinates = RouteReportController::getRouteCoordinates($url);
        $content = "";
        foreach ($coordinates as $coordinate) {
            $coordinate = (object)$coordinate;
            $content .= "$coordinate->latitude, $coordinate->longitude\n";
        }
        return $content;
    }
}
