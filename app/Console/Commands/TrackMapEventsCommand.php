<?php

namespace App\Console\Commands;

use App\Models\Company\Company;
use App\Services\API\Web\Track\TrackMapService;
use Illuminate\Console\Command;
use Log;
use Pusher\Pusher;
use Pusher\PusherException;

class TrackMapEventsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:map-events {--company=21}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command for send track events to map via Websockets';
    /**
     * @var TrackMapService
     */
    private $trackMapService;

    /**
     * @var Company
     */
    private $company;

    /**
     * Create a new command instance.
     *
     * @param TrackMapService $trackMapService
     */
    public function __construct(TrackMapService $trackMapService)
    {
        parent::__construct();
        $this->trackMapService = $trackMapService;
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
            '16f9596b985e59db2b9a',
            '98c90322e46cad4bf9b4',
            '763941',
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
        $this->logData("Send track data for route $routeId");
        $this->getPusher()->trigger("connection-" . $this->company->id, "track-route-$routeId", $data);
    }

    /**
     * Execute the console command.
     *
     * @throws PusherException
     */
    public function handle()
    {
        $this->company = Company::find($this->option('company'));

        if ($this->company) {
            foreach (range(1, 3) as $index) {
                $this->logData("Check for events: $index");
                $this->checkAndSendEvents();
                sleep(15);
            }
        } else {
            $this->logData("Company does'nt exists in DB!");
        }
    }

    /**
     * @throws PusherException
     */
    function checkAndSendEvents()
    {
        $trackData = collect($this->trackMapService->track($this->company->id, 0));

        $hasEvents = $trackData->filter(function ($track) {
                return ($track->speeding > 0 || $track->alertOffRoad || $track->alertParked);
            })->count();

        if ($hasEvents) {
            $this->logData("Has event! ...Sending track data!");
            $trackDataByRoutes = $trackData->groupBy('dispatchRegisterRouteId');

            foreach ($trackDataByRoutes as $routeId => $trackDataByRoute) {
                $chunkedData = $trackDataByRoute->chunk(8);
                foreach ($chunkedData as $track) {
                    $this->sendTrackData($track->values(), $routeId);
                }
            }
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
