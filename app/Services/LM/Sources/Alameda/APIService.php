<?php

namespace App\Services\LM\Sources\Alameda;

use function collect;

class APIService
{
    protected $type = 'Alameda';

    function getRoutes()
    {
        return collect([
            (object)[
                'id' => 1,
                'name' => 'RUTA 1'
            ]
        ]);
    }
}