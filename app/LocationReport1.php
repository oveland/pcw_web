<?php

namespace App;

use App\Traits\LocationReportTrait;
use Illuminate\Database\Eloquent\Model;

class LocationReport1 extends Model
{
    use LocationReportTrait;

    protected $table = 'location_reports_1';
}
