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

        return view('tools.map');
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
}
