<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class DatabaseMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:pcw-migrations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate tables fro OLD structure to NE platform';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*
         * Migrates gps types to gps vehicles
         *
         * TODO: Delete this sql once the gps_vehicles and sim_gps tables are fixed to a new structure
         */
        \DB::statement("UPDATE gps_vehicles as gv SET gps_type_id = ( SELECT gt.id FROM sim_gps as sg LEFT JOIN gps_types as gt ON (gt.name = sg.gps_type) WHERE sg.vehicle_id = gv.vehicle_id) WHERE  1 = 1");

        $client = new Client(['base_uri' => 'http://ne.pcwserviciosgps.com',]);

        $response = $client->get('migrate/companies');
        dump($response->getBody()->getContents());

        $response = $client->get('migrate/dispatches');
        dump($response->getBody()->getContents());

        $response = $client->get('migrate/routes');
        dump($response->getBody()->getContents());

        $response = $client->get('migrate/vehicles');
        dump($response->getBody()->getContents());

        $response = $client->get('migrate/users');
        dump($response->getBody()->getContents());

        return null;
    }
}
