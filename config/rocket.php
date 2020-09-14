<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 24/06/202o
 * Time: 11:23 PM
 */

use App\Models\Company\Company;

return [
    /*
    |--------------------------------------------------------------------------
    | Config for data from Rocket app
    |--------------------------------------------------------------------------
    */

    26 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 100),
                            'value' => 10
                        ]
                    ],
                    'contrast' => 5,
                    'gamma' => 2,
                    'sharpen' => 12
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 25),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(25, 50),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(50, 70),
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(70, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.5,                                # Min relation height/width for Large Detection
                        'mld' => 10,                                # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 3.5,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 100,                            # Percent of height when Normal Detection
                            'ld' => 95                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 62                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 15),
                            'value' => 10
                        ],
                        [
                            'range' => range(15, 100),
                            'value' => 20
                        ],
                    ],
                    'contrast' => 5,
                    'gamma' => 2,
                    'sharpen' => 12
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 25),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(25, 50),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(50, 70),
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(70, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.5,                                # Min relation height/width for Large Detection
                        'mld' => 2,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 3.5,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 200,                            # Percent of height when Normal Detection
                            'ld' => 100                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 50                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],
    Company::ALAMEDA => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 100),
                            'value' => 10
                        ],
                    ],
                    'contrast' => 5,
                    'gamma' => 2,
                    'sharpen' => 12
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 25),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(25, 50),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(50, 70),
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(70, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.5,                                # Min relation height/width for Large Detection
                        'mld' => 10,                                # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 3.5,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 100,                            # Percent of height when Normal Detection
                            'ld' => 95                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 62                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 15),
                            'value' => 10
                        ],
                        [
                            'range' => range(15, 100),
                            'value' => 20
                        ],
                    ],
                    'contrast' => 5,
                    'gamma' => 2,
                    'sharpen' => 12
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 25),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(25, 50),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(50, 70),
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(70, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.5,                                # Min relation height/width for Large Detection
                        'mld' => 2,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 3.5,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 200,                            # Percent of height when Normal Detection
                            'ld' => 100                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 50                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],
    Company::YUMBENOS => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 15),
                            'value' => 0
                        ],
                        [
                            'range' => range(15, 100),
                            'value' => 20
                        ]
                    ],
                    'contrast' => 8,
                    'gamma' => 1,
                    'sharpen' => 0
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 20),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(20, 40),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(40, 60),
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(60, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.4,                                # Min relation height/width for Large Detection
                        'mld' => 10,                                # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 40,                         # Min percent height for Overlap Detection
                            'rs' => 2.7,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent width for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 130,                            # Percent of height when Normal Detection
                            'ld' => 150                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 50                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ],
            'seating' => [

            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 10),
                            'value' => 0
                        ],
                        [
                            'range' => range(10, 15),
                            'value' => 10
                        ],
                        [
                            'range' => range(15, 100),
                            'value' => 20
                        ],
                    ],
                    'contrast' => 8,
                    'gamma' => 1,
                    'sharpen' => 0
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 25),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(25, 60),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(60, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.5,                                # Min relation height/width for Large Detection
                        'mld' => 2,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 3.5,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 240,                            # Percent of height when Normal Detection
                            'ld' => 240                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 50                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],
    Company::TUPAL => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 15),
                            'value' => 0
                        ],
                        [
                            'range' => range(15, 100),
                            'value' => 20
                        ]
                    ],
                    'contrast' => 8,
                    'gamma' => 1,
                    'sharpen' => 0
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 20),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(20, 40),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(40, 60),
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(60, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.4,                                # Min relation height/width for Large Detection
                        'mld' => 10,                                # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 40,                         # Min percent height for Overlap Detection
                            'rs' => 2.7,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent width for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 130,                            # Percent of height when Normal Detection
                            'ld' => 150                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 50                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ],
            'seating' => [

            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => range(0, 10),
                            'value' => 0
                        ],
                        [
                            'range' => range(10, 15),
                            'value' => 10
                        ],
                        [
                            'range' => range(15, 100),
                            'value' => 20
                        ],
                    ],
                    'contrast' => 8,
                    'gamma' => 1,
                    'sharpen' => 0
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => range(0, 25),
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(25, 60),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(60, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.5,                                # Min relation height/width for Large Detection
                        'mld' => 2,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 3.5,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 240,                            # Percent of height when Normal Detection
                            'ld' => 240                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 50                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ]
];