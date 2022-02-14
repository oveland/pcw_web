<?php

namespace App\Http\Controllers\Admin;

use App\Models\Routes\Route;
use App\Http\Controllers\Controller;

class ControlPointsController extends Controller
{
    function all(Route $route)
    {
        return $route->controlPoints->toJson();
    }
}
