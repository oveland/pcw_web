<?php

namespace App\Events\App\Rocket;

use App\Models\Vehicles\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class AppEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Vehicle
     */
    private $vehicle;
    private $data;

    public function __construct(Vehicle $vehicle, $data)
    {
        $this->vehicle = $vehicle;
        $this->data = $data;
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
        return "device-".$this->vehicle->plate;
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
