<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AlamedaJuneLocation extends Model
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
        return $this->hasOne(AlamedaJuneReport::class,'location_id','id');
    }

    /**
     * Check valid coordinates
     *
     * @return bool
     */
    public function isValid()
    {
        return ($this->latitude != 0 && $this->longitude != 0)?true:false;
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
