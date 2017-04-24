<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id_registro');
    }

    public function location()
    {
        return $this->belongsTo(Location::class,'location_id','id');
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
