<?php

namespace App\Http\Controllers\API;

use App\Services\API\Apps\MyRouteService;
use App\Services\API\Apps\PCWProprietaryService;
use App\Services\API\Apps\PCWTrackService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class APIController extends Controller
{
    public function serve($api, Request $request)
    {
        switch ($api) {
            case 'app-my-route':
                return MyRouteService::serve($request);
                break;
            case 'app-pcw-track':
                return PCWTrackService::serve($request);
                break;
            case 'app-pcw-proprietary':
                return PCWProprietaryService::serve($request);
                break;
            default:
                abort(403);
                break;
        }
    }
}
