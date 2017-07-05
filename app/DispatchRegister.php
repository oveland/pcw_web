<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DispatchRegister extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reports()
    {
        return $this->hasMany(Report::class,'dispatch_register_id','id')->orderBy('date','asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function locations()
    {
        return $this->hasMany(Location::class,'dispatch_register_id','id')->orderBy('date','asc');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
