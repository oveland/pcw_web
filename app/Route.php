<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'ruta';

    public function getNameAttribute(){
        return $this->attributes['nombre'];
    }
    public function getDistanceAttribute(){
        return $this->attributes['distancia'];
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
