<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

class LocationReport4 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_4';
}
