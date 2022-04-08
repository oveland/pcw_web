<?php

namespace App\Providers;

use DB;
use Illuminate\Support\Str;
use Log;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        // Add in boot function
//        DB::listen(function ($query) {
//
//            if (!Str::contains($query->sql, 'telescope')) {
//                \File::append(
//                    storage_path('/logs/query.log'),
//                    '[' . date('Y-m-d H:i:s') . ']' . PHP_EOL . $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL . PHP_EOL
//                );
//            }
//        });

        if(env('APP_ENV') !== 'local')
        {
            URL::forceScheme('https');
        }

        Paginator::useBootstrapThree();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
