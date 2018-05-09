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
        'seating' => [
            'distribution' => [
                'MBI-711' => [
                    'row1' => [
                        5 => [2, 3],
                        4 => [4, 5],
                        3 => [6, 7],
                        2 => [8, 9],
                        1 => [10]
                    ],
                    'row2' => [
                        10 => [13, 14],
                        9 => [15, 16],
                        8 => [17, 18],
                        7 => [19, 20],
                        6 => [21],
                    ]
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
                    ]
                ]
            ]
        ]
    ]
];