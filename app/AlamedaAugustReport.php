<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlamedaAugustReport extends Model
{
    public function dispatchRegister()
    {
        return $this->belongsTo(DispatchRegister::class,'dispatch_register_id','id');
    }

    public function location()
    {
        return $this->belongsTo(AlamedaAugustLocation::class,'location_id','id');
    }

    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    /*const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';*/
}
