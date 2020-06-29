<?php

namespace App\Services\Apps\Rocket\Photos;

class FacesZone extends PhotoZone
{
    public function getSeatingZoneInstance($profileSeat = null)
    {
        return new FacesZone($profileSeat);
    }
}