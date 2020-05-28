<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 28/05/2020
 * Time: 02:20 AM
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Config for rekognition service
    |--------------------------------------------------------------------------
    */
    'credentials' => [
        'rekognition' => [
            'key' => env('AWS_CREDENTIALS_REKOGNITION_KEY'),
            'secret' => env('AWS_CREDENTIALS_REKOGNITION_SECRET'),
        ]
    ]
];