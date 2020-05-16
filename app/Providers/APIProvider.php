<?php

namespace App\Providers;

use App\Services\API\Apps\MyRouteService;
use App\Services\API\Apps\PCWProprietaryService;
use App\Services\API\Apps\PCWTrackService;
use App\Services\API\Apps\RocketService;
use Illuminate\Support\ServiceProvider;

class APIProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('api.app', function ($app, $params) {
            $name = $params['name'];
            $service = $params['service'] ?? null;

            switch ($name) {
                case 'app-my-route':
                    return new MyRouteService($service);
                    break;

                case 'app-pcw-track':
                    return new PCWTrackService($service);
                    break;

                case 'app-pcw-proprietary':
                    return new PCWProprietaryService($service);
                    break;

                case 'rocket':
                case 'app-rocket':
                    return new RocketService($service);
                default:
                    abort(403);
                    break;
            }
        });

        $this->app->bind('pcw.web', function ($app, $params) {
            // TODO: Migrate hers ths 'web method' of API Controller ;)
        });
    }
}
