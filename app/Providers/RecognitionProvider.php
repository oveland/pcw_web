<?php

namespace App\Providers;

use App\Services\AWS\RekognitionService;
use App\Services\Recognition\RecognitionService;
use Illuminate\Support\ServiceProvider;

class RecognitionProvider extends ServiceProvider
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
        $this->app->bind('recognition.opencv', function () {
            return new RecognitionService();
        });

        $this->app->bind('recognition.aws', function () {
            return new RekognitionService();
        });
    }
}
