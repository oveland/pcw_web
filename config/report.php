<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 12/10/2018
 * Time: 8:55 PM
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Config for consolidated reports
    |--------------------------------------------------------------------------
    */
    'consolidated' => [
        'controlPoints' => [
            'withDelay' => [
                [
                    'routeId' => 136,       // RUTA 1
                    'controlPoints' => [
                        ['id' => 1380, 'maxTime' => '00:08:00'],     // Hospital Mario Correa
                        ['id' => 1382, 'maxTime' => '00:32:00'],     // Los Cambulos
                        ['id' => 1394, 'maxTime' => '02:50:00'],     // Despacho Regreso Final
                    ]
                ],
                [
                    'routeId' => 137,       // RUTA 3
                    'controlPoints' => [
                        ['id' => 1396, 'maxTime' => '00:08:00'],     // Hospital Mario Correa
                        ['id' => 1399, 'maxTime' => '00:32:00'],     // Los Cambulos
                        ['id' => 1406, 'maxTime' => '02:50:00'],     // Despacho Regreso Final
                    ]
                ],
                [
                    'routeId' => 135,       // RUTA 6
                    'controlPoints' => [
                        ['id' => 1303, 'maxTime' => '00:08:00'],     // Hospital Mario Correa
                        ['id' => 1463, 'maxTime' => '00:32:00'],     // Los Cambulos
                        ['id' => 1314, 'maxTime' => '02:50:00'],     // Control Final
                    ]
                ]
            ]
        ]
    ]
];