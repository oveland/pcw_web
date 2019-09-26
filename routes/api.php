<?php

use Illuminate\Http\Request;

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

Route::get('/{appName}', 'API\APIController@app');




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

Route::get('/exports/current-vehicle-status', function(){
    phpinfo();
});

