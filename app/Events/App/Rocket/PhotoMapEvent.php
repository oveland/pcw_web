<?php

namespace App\Events\App\Rocket;

use App\Models\Vehicles\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PhotoMapEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $data;
    /**
     * @var Vehicle
     */
    private $vehicle;

    /**
     * Create a new event instance.
     *
     * @param Vehicle $vehicle
     * @param $data
     */
    public function __construct(Vehicle $vehicle, $data)
    {
        $this->data = $data;
        $this->vehicle = $vehicle;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("photo");
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return "track-" . $this->vehicle->company_id;
    }

    /**
     * Data to broadcast
     *
     * @return array
     */
    public function broadcastWith()
    {
        return $this->data;
    }
}
