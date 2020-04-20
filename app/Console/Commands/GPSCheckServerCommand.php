<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\SMS;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GPSCheckServerCommand extends Command
{
    const ALERT_SMS_NUMBERS = [
        3145224312
    ];

    private $issues = [];
    private $now = '';

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
        $this->issues = collect([]);
        $this->now = Carbon::now()->toDateTimeString();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->issues = collect([]);
        $this->checkGPSServer();
        $this->checkDatabaseServer();
        $this->sendAlerts();
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
            $this->issues->push("GPS server id down! $this->now");
        }
    }

    public function checkDatabaseServer()
    {
        $dbOK = false;
        $dbSize = null;
        $ssdSize = 400;
        $usagePercent = 0;
        try {
            $dbSizeQuery = collect(DB::select("SELECT pg_size_pretty(pg_database_size('GPS')) db_size"))->first()->db_size;
            if ($dbSizeQuery) {
                $dbSize = intval(explode(' ', $dbSizeQuery)[0]);
                $usagePercent = ($dbSize * 100 / $ssdSize);
                if ($usagePercent <= 60) $dbOK = true;
            }

            if (!$dbOK) {
                $this->issues->push("DB size is $dbSize GB. Usage $usagePercent% $this->now");
            }
        } catch (\Exception $e) {
            $this->issues->push("Database server is down! $this->now");
        }
    }

    public function sendAlerts()
    {
        foreach ($this->issues as $issue){
            foreach (self::ALERT_SMS_NUMBERS as $number) {
                $this->info($issue);
                SMS::sendCommand($issue, $number);
            }
        }
    }
}
