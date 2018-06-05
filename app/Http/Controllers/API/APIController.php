<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Apps\AppMyRouteController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    public function serve($api, Request $request)
    {
        switch ($api) {
            case 'app-my-route':
                return AppMyRouteController::serve($request);
                break;
            default:
                abort(403);
                break;
        }
    }
}
