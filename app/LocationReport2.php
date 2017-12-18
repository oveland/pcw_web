<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

class LocationReport2 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_2';
}
