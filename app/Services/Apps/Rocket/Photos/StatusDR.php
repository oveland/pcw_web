<?php

namespace App\Services\Apps\Rocket\Photos;

use App\Models\Routes\DispatchRegister;

class StatusDR
{
    /**
     * @var DispatchRegister
     */
    public $dr;
    public $start;
    public $in;
    public $end;
    public $none;

    public $cp;

    public function __construct()
    {
        $this->start = false;
        $this->in = false;
        $this->end = false;
        $this->none = true;
        $this->text = 'none';
        $this->dr = null;

    }

    function isActive()
    {
        return $this->start || $this->in;
    }

    function isInactive()
    {
        return $this->end || $this->none;
    }

    function getRouteId()
    {
        return $this->dr ? $this->dr->route_id : null;
    }

    function getDRId()
    {
        return $this->dr ? $this->dr->id : null;
    }

    function getRoundTrip()
    {
        return $this->dr ? $this->dr->round_trip : null;
    }

    function getRouteName()
    {
        return $this->dr ? $this->dr->route->name : null;
    }

    function getFrom()
    {
        return $this->dr ? $this->dr->departure_time : null;
    }

    function getTo()
    {
        return $this->dr ? $this->dr->arrival_time : null;
    }
}