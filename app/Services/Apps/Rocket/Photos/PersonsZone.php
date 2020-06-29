<?php

namespace App\Services\Apps\Rocket\Photos;

class PersonsZone extends PhotoZone
{
    public function getSeatingZoneInstance($profileSeat = null)
    {
        return new PersonsZone($profileSeat);
    }
}