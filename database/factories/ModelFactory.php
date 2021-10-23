<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var Factory $factory */

use App\Models\LM\Mark;
use App\Models\LM\Trajectory;
use App\Models\LM\Turn;
use App\Models\Company\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factory;

/*$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => Str::random(10),
    ];
});*/

$company = Company::find(18);

$factory->define(Turn::class, function ($faker) use ($company) {
    $vehiclesId = $company->activeVehicles->pluck('id');
    $routesId = $company->activeRoutes->pluck('id');
    $driversId = $company->activeDrivers->pluck('id');
    return [
        'vehicle_id' => $vehiclesId[random_int(0, $vehiclesId->count() - 1)],
        'route_id' => $routesId[random_int(0, $routesId->count() - 1)],
        'driver_id' => $driversId[random_int(0, $driversId->count() - 1)],
    ];
});

$factory->define(Mark::class, function ($faker) use ($company) {

    $trajectories = Trajectory::all()->pluck('id');

    $date = $initialDate = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d') . " 06:00:00");
    $initialDate = $date->copy();
    $finalDate = $date->addMinutes(random_int(60, 120))->copy();

    $passengersUp = random_int(40, 50);
    $passengersDown = random_int(40, 50);

    $locks = random_int(1, 10);
    $auxiliaries = random_int(1, 10);

    $imBeaMax = $passengersUp + random_int(1, 10);
    $imBeaMin = $passengersDown - random_int(1, 10);

    $passengersBoarding = $passengersUp > $passengersDown ? ($passengersUp - $passengersDown) : 0;

    $passengersBEA = $passengersUp > $passengersDown ? $passengersUp : $passengersDown;
    $totalBEA = (($imBeaMax + $imBeaMin) / 2) * 2500;

    return [
        'turn_id' => function () {
            return factory(Turn::class)->create()->id;
        },
        'trajectory_id' => $trajectories[random_int(0, $trajectories->count() - 1)],
        'date' => Carbon::now(),
        'initial_time' => $initialDate,
        'final_time' => $finalDate,
        'passengers_up' => $passengersUp,
        'passengers_down' => $passengersDown,
        'locks' => $locks,
        'auxiliaries' => $auxiliaries,
        'boarded' => $passengersBoarding,
        'im_bea_max' => $imBeaMax,
        'im_bea_min' => $imBeaMin,
        'total_bea' => $totalBEA,
        'passengers_bea' => $passengersBEA
    ];
});
