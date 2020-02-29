<?php

namespace App\Events;

use App\Models\Company\Company;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TrackingMapEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $trackingLocations;
    private $company;
    private $route;

    /**
     * Create a new event instance.
     *
     * @param $company
     * @param $route
     * @param $trackingLocations
     */
    public function __construct($company, $route, $trackingLocations)
    {
        $this->trackingLocations = $trackingLocations;
        $this->company = $company;
        $this->route = $route;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("gps");
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return __NAMESPACE__."\\track-route-$this->route";
    }

    /**
     * Data to broadcast
     *
     * @return array
     */
    public function broadcastWith()
    {
        return $this->trackingLocations->toArray();
    }
}
