<?php

namespace App\Models\LM;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sync extends Model
{
    protected $table = 'lm_syncs';

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo
     */
    function company()
    {
        return $this->belongsTo(Company::class);
    }
}
