<?php

namespace App\Providers;

use App\Services\API\Apps\APIConcoxService;
use App\Services\API\Apps\MyRouteService;
use App\Services\API\Apps\PCWProprietaryService;
use App\Services\API\Apps\PCWTrackService;
use App\Services\API\Apps\APIRocketService;
use App\Services\API\Files\APIConcoxFilesService;
use App\Services\API\Files\APIRocketFilesService;
use App\Services\API\Web\APIPassengersService;
use App\Services\API\Web\DB\APIMigrationsService;
use App\Services\API\Web\Reports\APIReportService;
use App\Services\Apps\Rocket\Photos\PhotoService;
use File;
use Illuminate\Support\ServiceProvider;
use Image;

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
        // For access global API
        $this->app->bind('api', function ($app, $params) {
            $platform = $params['platform'];
            return app("api.$platform", $params);
        });

        // For API apps
        $this->app->bind('api.app', function ($app, $params) {
            $resource = $params['resource'];
            $service = $params['service'] ?? null;

            switch ($resource) {
                case 'my-route':
                case 'app-my-route':        // TODO: migrate/delete in client implementation
                    return new MyRouteService($service);
                    break;

                case 'track':
                case 'app-pcw-track':       // TODO: migrate/delete in client implementation
                    return new PCWTrackService($service);
                    break;

                case 'proprietary':
                case 'app-pcw-proprietary': // TODO: migrate/delete in client implementation
                    return new PCWProprietaryService($service);
                    break;

                case 'rocket':
                case 'app-rocket':
                    return new APIRocketService($service);
                    break;
                case 'concox':
                    return new APIConcoxService($service);
                    break;
                default:
                    return abort(403);
                    break;
            }
        });

        // For API Web
        $this->app->bind('api.web', function ($app, $params) {
            $resource = $params['resource'];
            $service = $params['service'] ?? null;

            switch ($resource) {
                case 'passengers':
                    return new APIPassengersService($service);
                    break;

                case 'reports':
                    return new APIReportService($service);
                    break;

                case 'migrations':
                    return new APIMigrationsService($service);
                default:
                    return abort(403);
                    break;
            }
        });

        // For API files
        $this->app->bind('api.files', function ($app, $params) {
            $resource = $params['resource'];
            $service = $params['service'] ?? null;

            switch ($resource) {
                case 'rocket':
                    return new APIRocketFilesService($service);
                    break;
                default:
                    return Image::make(File::get('img/image-404.jpg'))->resize(200, 200)->response('png');
                    break;
            }
        });
    }
}
