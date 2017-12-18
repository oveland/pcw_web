<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

class LocationReport6 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_6';
}
