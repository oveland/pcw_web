<?php

use Illuminate\Http\Request;

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

Route::any('/{name}', 'API\APIController@app');


/*
|--------------------------------------------------------------------------
| API Routes for WEB APPS
|--------------------------------------------------------------------------
|
| For request with central controller
|
*/

/* General route */
Route::get('/v1/{apiName}/{service}', 'API\APIController@web');




/*
|--------------------------------------------------------------------------
| Version 2 for API integrates only one controller and global method serve
|--------------------------------------------------------------------------
|
*/
Route::get('/v2/{api}/{name}/{service}', 'API\APIController@serve');

