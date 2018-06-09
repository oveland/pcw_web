<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class DatabaseManageOLDRoutinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:manage-old-routines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute routines for old platform. This logic is temporary implemented while migration processes are completed to NE';

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
        $client = new Client(['base_uri' => 'http://admin.pcwserviciosgps.com']);
        $response = $client->get('php/migrarContadorHistorialSeisMeses.php');
        dd($response->getBody()->getContents());
    }
}
