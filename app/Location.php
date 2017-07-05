<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function report()
    {
        return $this->hasOne(Report::class,'location_id','id');
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
