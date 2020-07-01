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
                    'brightness' => 10,
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
                    'brightness' => 10,
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
                    'brightness' => 10,
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
                    'brightness' => 10,
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
                    'brightness' => 10,
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
                            'range' => range(25, 40),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(40, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.4,                                # Min relation height/width for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 3.5,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 130,                            # Percent of height when Normal Detection
                            'ld' => 135                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 25,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => 20,
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
                            'range' => range(25, 40),
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => range(40, 100),
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 2.5,                                # Min relation height/width for Large Detection
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
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 35,                             # Percent top of point center when Normal Detection
                            'ld' => 50                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ]
];