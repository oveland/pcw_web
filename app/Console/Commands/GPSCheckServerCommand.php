<?php

namespace App\Console\Commands;

use App\Http\Controllers\API\SMS;
use App\Services\Server\GPSService;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GPSCheckServerCommand extends Command
{
    const ALERT_SMS_NUMBERS = [
        3145224312,
        3108844273
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
//        $this->checkRecognitionServer();

        $this->checkDatabaseServer();
        $this->checkTCPConnections();


        $this->sendAlerts();
    }

    public function checkRecognitionServer()
    {
        $isServerOK = false;
        try {
            sleep(10);
            $client = new Client(['base_uri' => 'http://100.20.192.70:5005/']);
            $response = $client->get('/');

            if ($response->getStatusCode() == 200) $isServerOK = true;
        } catch (\Exception $x) {
        }

        if (!$isServerOK) {
            $this->issues->push("Recognition Server OpenCV is down! $this->now");
        }
    }

    public function checkTCPConnections()
    {
        $connectionsOK = true;

        $gpsService = new GPSService();
        $ip = '52.38.73.219';
        $ports = [
//            914 => 'Coban Photo',
            912 => 'Coban',
            990 => 'Skypatrol',
//            999 => 'Ruptela',
//            9999 => 'Ruptela Aux',
            991 => 'Meitrack',
            994 => 'Antares',
            21 => 'FTP Server',
        ];

        foreach ($ports as $port => $server) {
//            $this->log("Testing TCP $port ($server)...");
            $test = $gpsService->testConnection($ip, $port);
            if (!$test) {
                $connectionsOK = false;

                $this->issues->push("TCP Port $port ($server) is down! $this->now");
            }

//            $this->log("    - $server Ok = $test");
        }

        return $connectionsOK;
    }

    public function checkGPSServer()
    {
        $isServerOK = false;
        try {
            $client = new Client(['base_uri' => 'https://server.pcwserviciosgps.com/']);
            $response = $client->get('/');

            if ($response->getStatusCode() == 200) $isServerOK = true;
        } catch (\Exception $x) {
        }

        if (!$isServerOK) {
            $this->issues->push("GPS server is down! $this->now");
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
                if ($usagePercent <= 75) $dbOK = true;
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
        foreach ($this->issues as $issue) {
            foreach (self::ALERT_SMS_NUMBERS as $number) {
                $this->log($issue);
                SMS::sendCommand($issue, $number);
            }
        }
    }

    public function log($message)
    {
        $this->info(" $this->now • $message");

        Log::useDailyFiles(storage_path() . '/logs/check.server.log', 3);
        Log::info($message);
    }
}
