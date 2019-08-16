<?php

namespace App\Providers;

use App\Services\BEA\Database;
use Illuminate\Support\ServiceProvider;

class BEAServiceProvider extends ServiceProvider
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
        $this->app->singleton('beadb', function () {
            return new Database(config('database.connections.beadb.path'), config('database.connections.beadb.username'), config('database.connections.beadb.password'));
        });
    }
}
