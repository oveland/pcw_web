<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

class LocationReport5 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_5';
}
