<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'GPS'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
        ],

        'mysql' => [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],

        'GPS' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
            'timezone' => 'America/Bogota',
        ],

        'GPS_MONTH' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE_MONTH', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'GPS_SIX_MONTH' => [
            'driver' => 'pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '5432'),
            'database' => env('DB_DATABASE_SIX_MONTH', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'schema' => 'public',
            'sslmode' => 'prefer',
        ],

        'BEA' => [
            'COODETRANS' => [
                'db_id' => 1,
                'company_id' => App\Models\Company\Company::COODETRANS,
                'driver' => 'firebird',
                'path' => env('BEADB_PATH'),
                'username' => env('BEADB_USERNAME', 'BEADMIN'),
                'password' => env('BEADB_PASSWORD', 'bea00001'),
            ],

            'COODETRANS_RUTA_NORTE' => [
                'db_id' => 2,
                'company_id' => App\Models\Company\Company::COODETRANS,
                'driver' => 'firebird',
                'path' => env('BEADB_PATH_RUTA_NORTE'),
                'username' => env('BEADB_USERNAME', 'BEADMIN'),
                'password' => env('BEADB_PASSWORD', 'bea00001'),
            ],

            'MONTEBELLO' => [
                'db_id' => 1,
                'company_id' => App\Models\Company\Company::MONTEBELLO,
                'driver' => 'firebird',
                'path' => env('BEADB_PATH_MONTEBELLO'),
                'username' => env('BEADB_USERNAME_MONTEBELLO', 'BEADMIN'),
                'password' => env('BEADB_PASSWORD_MONTEBELLO', 'bea00001'),
            ],

            'ALAMEDA' => [
                'db_id' => 1,
                'company_id' => App\Models\Company\Company::ALAMEDA,
                'driver' => 'firebird',
                'path' => env('BEADB_PATH_ALAMEDA'),
                'username' => env('BEADB_USERNAME_ALAMEDA', 'BEADMIN'),
                'password' => env('BEADB_PASSWORD_ALAMEDA', 'bea00001'),
            ],

            'PCW' => [
                'db_id' => 1,
                'company_id' => App\Models\Company\Company::PCW,
                'driver' => 'firebird',
                'path' => env('BEADB_PATH_MONTEBELLO'),
                'username' => env('BEADB_USERNAME_MONTEBELLO', 'BEADMIN'),
                'password' => env('BEADB_PASSWORD_MONTEBELLO', 'bea00001'),
            ],
        ],
        'LM' => [
            'EXPRESO_PALMIRA' => [
                'db_id' => 1,
                'company_id' => App\Models\Company\Company::EXPRESO_PALMIRA,
                'driver' => 'sqlsrv',
                'host' => env('DFSDB_HOST_EP'),
                'port' => env('DFSDB_PORT_EP'),
                'database' => env('DFSDB_DB_EP'),
                'username' => env('DFSDB_USERNAME_EP'),
                'password' => env('DFSDB_PASSWORD_EP'),
                'prefix' => '',
                'charset' => 'SQL_Latin1_General_CP1_CI_AS',
                'pooling' => false,
            ],
            'DFS_EXPRESO_PALMIRA' => [
                'db_id' => 2,
                'company_id' => App\Models\Company\Company::EXPRESO_PALMIRA,
                'driver' => 'sqlsrv',
                'host' => env('DFSDB_HOST_EP'),
                'port' => env('DFSDB_PORT_EP'),
                'database' => env('DFSDB_DB_EP'),
                'username' => env('DFSDB_USERNAME_EP'),
                'password' => env('DFSDB_PASSWORD_EP'),
                'prefix' => '',
                'charset' => 'SQL_Latin1_General_CP1_CI_AS',
                'pooling' => false,
            ],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => 'predis',

        'default' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => 0,
        ],

    ],

    'total_pagination' => env('DB_TOTAL_PAGINATE', 15),
];
