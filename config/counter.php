<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 12/03/2018
 * Time: 10:55 AM
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Config for counter by recorder
    |--------------------------------------------------------------------------
    */
    'recorder' => [
        /*
        |--------------------------------------------------------------------------
        | Threshold fot low count
        |--------------------------------------------------------------------------
        */
        'threshold_low_count' => 200
    ],
    'sensor' => [
        'distribution' => [
            'gualas' => [
                'MBI-711' => [
                    'row1' => [
                        'Sensor 1' => [21],
                        'Sensor 2' => [22],
                        'Sensor 3' => [23],
                        'Sensor 4' => [24],
                        'Sensor 5' => [17],
                    ],
                    'row2' => [
                        'Sensor 13' => [9],
                        'Sensor 14' => [10],
                        'Sensor 15' => [11],
                        'Sensor 16' => [12],
                        'Sensor 17' => [5]
                    ],
                    'center' => ['Centro' => [15]],
                    'window' => ['Ventana' => [3]]
                ],
                'FAE-838' => [
                    'row1' => [
                        5 => [1, 2],
                        4 => [3, 4],
                        3 => [5, 6],
                        2 => [7, 8],
                        1 => [9]
                    ],
                    'row2' => [
                        10 => [12, 13],
                        9 => [14, 15],
                        8 => [16, 17],
                        7 => [18, 19],
                        6 => [20],
                    ],
                    'center' => [
                        11 => [3]
                    ],
                    'window' => [
                        12 => [4]
                    ]
                ],
                'VJB-336' => [
                    'row1' => [
                        5 => [4],
                        4 => [5],
                        3 => [6],
                        2 => [7],
                        1 => [8]
                    ],
                    'row2' => [
                        10 => [16],
                        9 => [17],
                        8 => [18],
                        7 => [19],
                        6 => [20],
                    ],
                    'center' => [
                        11 => [3]
                    ],
                    'window' => [
                        12 => [4]
                    ]
                ]
            ]
        ]
    ]
];