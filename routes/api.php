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
    dd('GOs');
    return $request->user();
});

Route::get('/{appName}', 'API\APIController@app');
Route::get('/v1/{apiName}/{service}', 'API\APIController@web');

Route::prefix('peak-and-plate')->group(function () {
    Route::get('/{company}', 'ApiPeakAndPlateController@getVehiclesCurrentPeakAndPlate');
});