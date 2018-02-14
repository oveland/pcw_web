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
        /*
        $counts = \DB::select("
            SELECT *
            FROM contador_eventos
            WHERE fecha = '2018-02-09' AND id_gps = 'M-1614'
            ORDER BY hora ASC
        ");

        $updated = 0;
        $lastTotal = 0;
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
        
        dump("** TOTAL ROWS: ".count($counts));
        dd("** TOTAL UPDATED: $updated");*/
        
        return view('tools.map');
    }

    public static function getAverageFromTORRESCriterion($frame)
    {
        $f = explode(" ","0 $frame");

        $i = collect([]);
        $i->push(0);
        $i->push(($f[2] + $f[3]) / 2);                  // 1°
        $i->push($f[4] + $f[5] - $f[6] - $f[7]);        // 2°
        $i->push($f[6] + $f[7] + $f[8]);                // 3°
        $i->push($f[9] + $f[10] - $f[11] - $f[12]);     // 4°
        $i->push($f[11] + $f[12] + $f[13]);             // 5°
        $i->push($f[14] + $f[15] + $f[16]);             // 6°
        $i->push($f[17] + $f[18] + $f[19]);             // 7°
        $i->push(($f[20] + $f[21]) / 2);                // 8°
        $i->push(($f[22] + $f[23]) / 2);                // 9°

        return intval(($i->get(2) + $i->get(3) + $i->get(4) + $i->get(5) + 2 * ($i->get(6) + $i->get(7) + $i->get(8) + $i->get(9)) )/12);
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
