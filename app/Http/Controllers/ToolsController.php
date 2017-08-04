<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;

class ToolsController extends Controller
{
    public function map()
    {
        dd(Geolocation::getDistance(3.3926260399991, -76.557045757967,3.3898998991945, -76.557799266885));
        return view('tools.map');
    }
}
