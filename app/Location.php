<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_format');
    }

    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'last_updated';
}
