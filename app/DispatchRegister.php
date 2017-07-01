<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DispatchRegister extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    public function reports()
    {
        return $this->hasMany(Report::class,'dispatch_register_id','id')->orderBy('date','asc');
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
