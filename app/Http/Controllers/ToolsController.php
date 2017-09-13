<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Utils\Geolocation;
use Carbon\Carbon;

class ToolsController extends Controller
{
    public function map()
    {
        return view('tools.map');
    }
}
