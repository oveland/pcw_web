<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

class Vehicle extends Model
{
    use Mapping;
    protected $table = 'crear_vehiculo';
    /**
     * @var array
     */
    protected $mapping = [
        'id' =>  'vehicle.id_crear_vehiculo',
        'propertyExpeditionDate' => 'fecha_ex_propiedad',
        'numberProperty' => 'vehicle.n_propiedad'
    ];

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
