<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlamedaJulyReport extends Model
{
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id');
    }

    public function location()
    {
        return $this->belongsTo(AlamedaJulyLocation::class,'location_id','id');
    }

    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    /*const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';*/
}
