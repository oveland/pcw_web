<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\SMS;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GPSCheckServerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gps:check-server';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if GPS server status is OK reading server ports. Send SMS if this is down';

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
        $this->checkGPSServer();
    }

    public function checkGPSServer()
    {
        $isServerOK = false;
        try {
            $client = new Client(['base_uri' => 'http://server.pcwserviciosgps.com']);
            $response = $client->get('/');

            if ($response->getStatusCode() == 200) $isServerOK = true;
        } catch (\Exception $x) {
        }

        if (!$isServerOK) {
            $now = Carbon::now();
            $alertMessage = "GPS server id down! " . $now->toDateTimeString();
            $this->info($alertMessage);
            SMS::sendCommand($alertMessage, "3145224312");
        }
    }
}
