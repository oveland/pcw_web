<?php

namespace App\Models\Apps\Rocket;

use Illuminate\Database\Eloquent\Model;

class SyncStatus extends Model
{
    protected $table = 'app_sync_status';
    
    protected $fillable = ['imei', 'busy'];

    protected $dates = ['updated_at', 'created_at'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }
}
