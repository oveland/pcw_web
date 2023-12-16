<?php

namespace App\Services\Apps\Rocket;

use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\Routes\DispatchRegister;
use App\Models\Routes\Route;
use App\Models\Vehicles\Vehicle;

class ConfigProfileService
{
    protected const THRESHOLD_ACTIVATE = 2;
    protected const THRESHOLD_RELEASE = 2;

    /**
     * @var Vehicle
     */
    private $vehicle;

    /**
     * @var ProfileSeat
     */
    private $profileSeat;

    /**
     * @var ConfigProfile
     */
    private $configProfile;

    /**
     * @param ProfileSeat $profileSeat
     * @param ConfigProfile $configProfile
     */
    function __construct(ProfileSeat $profileSeat, ConfigProfile $configProfile = null)
    {
        $this->profileSeat = $profileSeat;
        $this->vehicle = $this->profileSeat->vehicle;
        $this->configProfile = $configProfile;
    }

    /**
     * @param $type
     * @return object
     */
    function type($type)
    {
        $c = json_decode(json_encode($this->getConfigProfile()->type($type), JSON_FORCE_OBJECT), false);
        return $c;
    }

    /**
     * @return ConfigProfile
     */
    function getConfigProfile()
    {
//        $this->configProfile->config = $this->buildConfigProfile($this->profileSeat->camera);
        return $this->configProfile;
    }

    /**
     * @return array
     */
    function buildConfigProfile($camera)
    {
        $config = config('rocket.' . $this->vehicle->company_id);

        $seatingConfig = [];
        foreach ($this->profileSeat->occupation as $seat) {
            $number = $seat['number'];

            $rActivate = $this->profileSeat->persistence['activate'];
            $rRelease = $this->profileSeat->persistence['release'];

            $activate = $rActivate ?: self::THRESHOLD_ACTIVATE;
            $release = $rRelease ?: self::THRESHOLD_RELEASE;

            if ($this->vehicle->id == 2614) {
                if ($camera == '3') {
                    $activate = 3;
                    $release = 4;
                }
                if (collect([20])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 2;
                    $release = 48;
                }
            }

            // dd($vehicleRoute);

            if ($this->vehicle->id == 2563) { // VH 5015 valledupar   OJO!!!!!!
                if (collect([1, 7, 15])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 40;
                }
            }
            if ($this->vehicle->id == 2576) { // VH 5013 valledupar   OJO!!!!!!
                if (collect([1])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 40;
                }
            }
            if ($this->vehicle->id == 2574) { // VH 5020 valledupar   OJO!!!!!!
                if (collect([2])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 40;
                }
            }

            if ($this->vehicle->id == 2582) { // VH 62 Armenia   OJO!!!!!!
                if (collect([15, 16, 17, 18])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 20;
                }
            }

            if ($this->vehicle->id == 2601) { // VH 041 IBAGUE   OJO!!!!!!
                if (collect([1])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 20;
                }
            }
            if ($this->vehicle->id == 2556) { // VH  5000 valledupar   OJO!!!!!!
                if (collect([1, 2])->contains(intval($number))) { // Asientos con poca cobertura
                    $activate = 1;
                    $release = 8;

                }
            }


//            if (collect([9, 10, 11, 12, 13, 14, 19, 20, 21,22, 23])->contains(intval($number))) {
//                $activate = $rActivate ? $rActivate : 5;
//                $release = $rActivate ? $rActivate : 5;
//            }
//
//            if (collect([15, 16, 17, 18, ])->contains(intval($number))) {
//                $activate = $rActivate ? $rActivate : 1;
//                $release = $rActivate ? $rActivate : 3;
//            }
            $persistenceRoutes = null;
            if ($this->vehicle->company_id == 39) {
                $persistenceRoutes = [
                    285 => ['a' => 5, 'r' => 30], // CALI - PEREIRA
                    286 => ['a' => 5, 'r' => 30], // PEREIRA - CALI

                    287 => ['a' => 5, 'r' => 30], // CALI - ARMENIA
                    288 => ['a' => 5, 'r' => 30], // ARMENIA - CALI

                    271 => ['a' => 2, 'r' => 18], // CALI - PALMIRA
                    272 => ['a' => 2, 'r' => 18], // PALMIRA - CALI

                    //282 => ['a' => 2, 'r' => 20], // CALI - AEROPUERTO
                    //283 => ['a' => 2, 'r' => 20], // AEROPUERTO - CALI
                ];
            }

            /**
             * Params para Topologías 2 (Criterio de Llenado de vasos)
             * En este criterio solo de toma el umbral de activación (Parámetro countFrom)
             * Los parámetros de $configT2 son procesados en SeatOccupationService: función getConfigT2().
             *
             * countFrom: Umbral de mínimo de detecciones en el asiento para poderlo contar (Vaso lleno)
             */
            $configT2 = ['default' => ['countFrom' => 2, 'complementsT1' => false]];
            if ($this->vehicle->company_id == 39) {
                $configT2 = [
                    'default' => ['countFrom' => 2, 'complementsT1' => true], // Conf por defecto a todas las rutas
                    'defaultLargeRoutes' => ['countFrom' => 10, 'complementsT1' => true], // Para rutas largas (Ver modelo Route.php función isLarge())
                    'routes' => [
                        337 => ['countFrom' => 3, 'complementsT1' => true], // CALI - BOGOTA
                        338 => ['countFrom' => 3, 'complementsT1' => true], // BOGOTA - CALI
                    ]
                ];
            }

            $seatingConfig[$number] = [
                'persistence' => compact(['activate', 'release']),
                'persistenceRoutes' => $persistenceRoutes,
                'T2' => $configT2
            ];
        }

        $config['seating'] = $seatingConfig;

        $widthSeatingAverage = collect($this->profileSeat->occupation)->average('width');

        $cameraConfig = [
            'processWidthSize' => [
                'faces' => [
                    'maxWidth' => 0.6 * $widthSeatingAverage,
                    'minWidth' => 0.1 * $widthSeatingAverage,
                ],
                'persons' => [
                    'maxWidth' => 1.8 * $widthSeatingAverage,
                    'minWidth' => 0.5 * $widthSeatingAverage,
                ]
            ],
//            'processWidthSize' => [
//                'faces' => [
//                    'maxWidth' => 2 * $widthSeatingAverage,
//                    'minWidth' => 0 * $widthSeatingAverage,
//                ],
//                'persons' => [
//                    'maxWidth' => 2 * $widthSeatingAverage,
//                    'minWidth' => 0 * $widthSeatingAverage,
//                ]
//            ],
            'largeDetection' => true,
            'widthSeatingAverage' => $widthSeatingAverage
        ];

        $config['camera'] = $cameraConfig;

        return $config;
    }
}
