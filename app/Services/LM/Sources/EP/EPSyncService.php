<?php

namespace App\Services\LM\Sources\EP;

use App\Facades\EPDB;
use App\Models\Vehicles\Vehicle;
use App\Services\LM\SyncService;

class EPSyncService extends SyncService
{
    protected $type = 'dfs';

    function locations(Vehicle $vehicle, $date)
    {
    }

    function turns()
    {
    }

    function vehicles()
    {
    }

    function drivers()
    {
    }

    function trajectories()
    {
    }

    function marks()
    {
    }

    function routes()
    {
    }

    function test()
    {
        EPDB::select("SELECT SYSDATE FROM DUAL");

        dump('Sync EP data count!!!!!!');
    }
}