<?php

namespace App\Events\App;

use App\Models\Vehicles\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RocketAppEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Vehicle
     */
    private $vehicle;
    private $type;

    public function __construct(Vehicle $vehicle, $type)
    {
        $this->vehicle = $vehicle;
        $this->type = $type;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new Channel("app-rocket");
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return "request-photo-".$this->vehicle->plate;
    }

    /**
     * Data to broadcast
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['type' => $this->type];
    }
}
