<?php

namespace App\Http\Controllers\API;

use App;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Container\BindingResolutionException;

class APIController extends Controller
{
    /**
     * API for mobile apps
     * @param $resource
     * @return mixed|api.app
     * @throws BindingResolutionException
     * @api v1.0
     *
     */
    public function app($resource)
    {
        return App::makeWith('api.app', compact('resource'))->serve();
    }

    /**
     * API for web services
     * @param $resource
     * @param $service
     * @return JsonResponse
     * @throws BindingResolutionException
     * @api v1.0
     *
     */
    public function web($resource, $service)
    {
        return App::makeWith('api.web', compact(['resource', 'service']))->serve();
    }

    /**
     * Inbound main API
     * @param $platform
     * @param $resource
     * @param $service
     * @return JsonResponse
     * @throws BindingResolutionException
     * @api v2.0
     *
     * @example For API Apps:   /v2/app/rocket/get-photo?side=rear
     * @example For API Web:    /v2/web/reports/control-points?foo=bar
     */
    public function serve($platform, $resource, $service)
    {
        if (collect(['app', 'web'])->contains($platform) && $resource && $service) {
            return App::makeWith("api", compact(['platform', 'resource', 'service']))->serve();
        }

        return response()->json([
            'success' => false,
            'error' => true,
            'message' => 'Platform or Resource or Service not specified yet'
        ]);
    }
}
