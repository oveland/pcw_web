<?php

use App\Models\Company\Company;

return [
    /*
    |--------------------------------------------------------------------------
    | Config for data from Rocket app
    |--------------------------------------------------------------------------
    */

    Company::PCW => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 58],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [58, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 3.5,                                # Min relation height/width for Large Detection
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 58],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [58, 100],
                            'color' => '#05da55',
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
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],

    2 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 58],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [58, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 3.5,                                # Min relation height/width for Large Detection
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 58],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [58, 100],
                            'color' => '#05da55',
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
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],

    26 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 100],
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
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 50],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [50, 70],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [70, 100],
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
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
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 50],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [50, 70],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [70, 100],
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 60],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 100],
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 50],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [50, 100],
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
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],
    Company::MONTEBELLO => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 30],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [30, 60],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 80],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [80, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 6,                                # Min relation height/width for Large Detection
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 30],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [30, 75],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [75, 80],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [80, 100],
                            'color' => '#05da55',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 5,                                # Min relation height/width for Large Detection
                        'mld' => 5,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 1.0,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 200,                            # Percent of height when Normal Detection
                            'ld' => 200                             # Percent of height when Large Detection
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
    Company::COODETRANS => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 60],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 3.5,                                # Min relation height/width for Large Detection
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 50],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [50, 100],
                            'color' => '#05da55',
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
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 0
                        ],
                        [
                            'range' => [15, 100],
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
                            'range' => [0, 20],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [20, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [40, 60],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [60, 100],
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
            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 10],
                            'value' => 0
                        ],
                        [
                            'range' => [10, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
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
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 60],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [60, 100],
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
    39 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 30],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [30, 60],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 80],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [80, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 6,                                # Min relation height/width for Large Detection
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 30],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [30, 75],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [75, 80],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [80, 100],
                            'color' => '#05da55',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 5,                                # Min relation height/width for Large Detection
                        'mld' => 5,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 1.0,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 200,                            # Percent of height when Normal Detection
                            'ld' => 200                             # Percent of height when Large Detection
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
    41 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 35],
                            'color' => 'blue',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [35, 60],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 70],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [70, 90],
                            'color' => 'green',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [90, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 5,                                # Min relation height/width for Large Detection
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
                            'ld' => 100                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 75,                             # Percent top of point center when Normal Detection
                            'ld' => 50                             # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 20],
                            'color' => 'blue',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [20, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 50],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [50, 100],
                            'color' => '#05da55',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 5,                                # Min relation height/width for Large Detection
                        'mld' => 5,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 1.0,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 200,                            # Percent of height when Normal Detection
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],
    42 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 40],
                            'color' => 'blue',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [40, 60],
                            'color' => 'black',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 90],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [90, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 3,                                # Min relation height/width for Large Detection
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 20],
                            'color' => 'blue',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [20, 50],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [50, 90],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [90, 100],
                            'color' => '#05da55',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 1.0,                                # Min relation height/width for Large Detection
                        'mld' => 2,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 1.0,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 200,                            # Percent of height when Normal Detection
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],

    43 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 20],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [20, 60],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 80],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [80, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 2,                                # Min relation height/width for Large Detection
                        'mld' => 1,                                # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 1.0,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 50],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [50, 75],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [75, 80],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [80, 100],
                            'color' => '#05da55',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true
                        ]
                    ],
                    'box' => [
                        'ld' => 2,                                # Min relation height/width for Large Detection
                        'mld' => 1,                                 # Min width percent (about of image size) for Large Detection
                        'od' => [
                            'width' => 10,                          # Min percent width for Overlap Detection
                            'height' => 60,                         # Min percent height for Overlap Detection
                            'rs' => 1.0,                            # Min relation height/width for Overlap Detection
                            'rsw' => 15,                            # Min percent height for Overlap Detection when rs
                        ]
                    ],
                    'draw' => [
                        'heightFromWidth' => [
                            'nd' => 200,                            # Percent of height when Normal Detection
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 10,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],

    35 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 0
                        ],
                        [
                            'range' => [15, 100],
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
                            'range' => [0, 20],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [20, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [40, 60],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [60, 100],
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
            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 10],
                            'value' => 0
                        ],
                        [
                            'range' => [10, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
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
                            'range' => [0, 20],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [20, 60],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [60, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 1.0,                                # Min relation height/width for Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 20],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [20, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [40, 60],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [60, 100],
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
            ]
        ],
        'faces' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 10],
                            'value' => 10
                        ],
                        [
                            'range' => [10, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ],
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 60],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [60, 100],
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

    29 => [
        'persons' => [
            'photo' => [
                'effects' => [
                    'brightness' => [
                        [
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 90],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [90, 100],
                            'color' => '#9bef00',
                            'background' => 'rgba(122, 162, 12, 0.1)',
                            'count' => true,
                        ]
                    ],
                    'box' => [
                        'ld' => 3.5,                                # Min relation height/width for Large Detection
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
                            'nd' => 120,                            # Percent of height when Normal Detection
                            'ld' => 150                              # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 50,                             # Percent top of point center when Normal Detection
                            'ld' => 40                              # Percent top of point center when Large Detection
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
                            'range' => [0, 15],
                            'value' => 10
                        ],
                        [
                            'range' => [15, 100],
                            'value' => 10
                        ]
                    ],
                    'contrast' => -30,
                    'gamma' => 1,
                    'sharpen' => 50,
                ],
                'rekognition' => [
                    'rules' => [
                        [
                            'range' => [0, 15],
                            'color' => 'red',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => false
                        ],
                        [
                            'range' => [15, 40],
                            'color' => 'orange',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [40, 90],
                            'color' => 'yellow',
                            'background' => 'rgba(137, 138, 135, 0.1)',
                            'count' => true
                        ],
                        [
                            'range' => [90, 100],
                            'color' => '#05da55',
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
                            'ld' => 200                             # Percent of height when Large Detection
                        ],
                        'centerTopFromHeight' => [
                            'nd' => 40,                             # Percent top of point center when Normal Detection
                            'ld' => 30                              # Percent top of point center when Large Detection
                        ]
                    ]
                ]
            ]
        ]
    ],
];
