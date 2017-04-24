<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $table = 'ruta';

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
