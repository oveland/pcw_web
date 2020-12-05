<?php

namespace App\Providers;

use App\Services\Apps\Rocket\ConfigProfileService;
use App\Services\Apps\Rocket\Photos\FacesRekognitionService;
use App\Services\Apps\Rocket\Photos\FacesZone;
use App\Services\Apps\Rocket\Photos\PersonsRekognitionService;
use App\Services\Apps\Rocket\Photos\PersonsZone;
use Illuminate\Support\ServiceProvider;

class RocketServiceProvider extends ServiceProvider
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
        $this->app->bind('rocket.photo.rekognition.persons', function ($app, $params) {
            $profileSeating = $params['profileSeating'];
            $zoneDetected = new PersonsZone();

            return new PersonsRekognitionService($zoneDetected, $profileSeating);
        });

        $this->app->bind('rocket.photo.rekognition.faces', function ($app, $params) {
            $profileSeating = $params['profileSeating'];
            $zoneDetected = new FacesZone();

            return new FacesRekognitionService($zoneDetected, $profileSeating);
        });
    }
}
