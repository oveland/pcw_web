<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

class Vehicle extends Model
{
    use Eloquence, Mappable;
    protected $table = 'crear_vehiculo';
    /**
     * @var array
     */
    protected $maps = [
        'id' =>  'vehicle.id_crear_vehiculo',
        'propertyExpeditionDate' => 'fecha_ex_propiedad',
        'numberProperty' => 'vehicle.n_propiedad'
    ];

    public function vehicle()
    {
        return $this->belongsTo(CrearVehiculo::class); // *
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
