<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\Rocket\PhotoUrlController;

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'API\AuthController@login');
    Route::post('register', 'API\AuthController@register');
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'API\AuthController@logout');
        Route::get('user', 'API\AuthController@user');
    });
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/*
|--------------------------------------------------------------------------
| API Routes for MOBILE APPS
|--------------------------------------------------------------------------
|
| For request with central controller
|
*/




/*
|--------------------------------------------------------------------------
| API Routes for WEB APPS
|--------------------------------------------------------------------------
|
| For request with central controller
|
*/

/* General route */



/*
|--------------------------------------------------------------------------
| Version 2 for API integrates only one controller and global method serve
|--------------------------------------------------------------------------
|
*/

Route::any('/v2/test', 'API\APIController@test');

Route::match(['get', 'post'], '/v2/rocket/photos/urls', [PhotoUrlController::class, 'getUrls']);
Route::match(['get', 'post'], '/v2/rocket/photos/t-urls', [PhotoUrlController::class, 'getTUrls']);
Route::post('/v2/rocket/photos/count5g', [PhotoUrlController::class, 'updateCount5gV2']);

// Luego (después) tu catch-all
Route::any('/{resource}', 'API\APIController@app');
Route::any('/v1/{resource}/{service}', 'API\APIController@web');
Route::any('/v2/{platform}/{resource}/{service}', 'API\APIController@serve');

