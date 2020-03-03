<?php

namespace App\Console\Commands;

use App\Events\TrackingMapEvent;
use App\Models\Company\Company;
use App\Services\API\Web\Track\TrackMapService;
use Illuminate\Console\Command;
use Log;
use Pusher\Pusher;
use Pusher\PusherException;

class TrackMapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:map {--company=21} {--driver=echo} {--delay=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send track to map via Websockets';
    /**
     * @var TrackMapService
     */
    private $trackMapService;

    /**
     * @var Company
     */
    private $company;


    /**
     * Driver broadcaster
     *
     * @var $driver
     */
    private $driver;

    /**
     * Create a new command instance.
     *
     * @param TrackMapService $trackMapService
     */
    public function __construct(TrackMapService $trackMapService)
    {
        parent::__construct();
        $this->trackMapService = $trackMapService;
        $this->driver = 'echo';
    }

    /**
     * @return Pusher
     * @throws PusherException
     */
    function getPusher()
    {
        $options = array(
            'cluster' => 'us2',
            'useTLS' => true
        );

        return new Pusher(
            'de6631a2b7d51bd2446f',
            '7fc305412582c72b04bd',
            '771253',
            $options
        );
    }

    /**
     * @param $data
     * @param $routeId
     * @throws PusherException
     */
    function sendTrackData($data, $routeId)
    {
        $this->getPusher()->trigger("track-route-$routeId", "gps", $data);
    }

    /**
     * Execute the console command.
     *
     * @throws PusherException
     */
    public function handle()
    {
        foreach (range(1, 3) as $index) {
            $this->track();
            sleep(16);
        }
    }

    /**
     * Execute the console command.
     *
     * @throws PusherException
     */
    public function track()
    {
        $delay = $this->option('delay');
        if ($delay) sleep($delay);

        $requestDriver = $this->option('driver');
        if ($requestDriver) $this->driver = $requestDriver;

        $this->company = Company::find($this->option('company'));

        if ($this->company) {
            $routes = $this->company->activeRoutes;

            $this->logData("Sending track data via <<$this->driver>> driver");

            foreach ($routes as $route) {
                $track = collect($this->trackMapService->track($this->company->id, $route->id));
                if ($this->driver == 'echo') {
                    event(new TrackingMapEvent($this->company->id, $route->id, $track->values()));
                } else if ($this->driver == 'pusher') {
                    $trackChunked = $track->chunk(8);
                    foreach ($trackChunked as $trackShort) {
                        $this->sendTrackData($trackShort->values(), $route->id);
                    }
                }
            }
        } else {
            $this->logData("Company does'nt exists in DB!");
        }
    }

    /**
     * @param $message
     * @param string $level
     */
    public function logData($message, $level = 'info')
    {
        $infoCompany = $this->company ? $this->company->short_name : $this->option('company');
        $message = "TRACK MAP ($infoCompany): $message";
        $this->info($message);
        switch ($level) {
            case 'warning':
                Log::warning($message);
                break;
            case 'error':
                Log::error($message);
                break;
            default:
                Log::info($message);
                break;
        }
    }
}
