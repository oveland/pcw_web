<?php

namespace App;

use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\ReportVehicleStatus;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\LastLocation
 *
 * @property int $id
 * @property int|null $version
 * @property string|null $date
 * @property string|null $date_created
 * @property int|null $dispatch_register_id
 * @property float|null $distance
 * @property string|null $last_updated
 * @property float|null $latitude
 * @property float|null $longitude
 * @property float|null $odometer
 * @property bool|null $off_road
 * @property float|null $orientation
 * @property int|null $reference_location_id
 * @property float|null $speed
 * @property string|null $status
 * @property int|null $vehicle_id
 * @property int|null $vehicle_status_id
 * @property float|null $yesterday_odometer
 * @property float|null $current_mileage
 * @property bool|null $speeding
 * @method static Builder|\App\LastLocation whereCurrentMileage($value)
 * @method static Builder|\App\LastLocation whereDate($value)
 * @method static Builder|\App\LastLocation whereDateCreated($value)
 * @method static Builder|\App\LastLocation whereDispatchRegisterId($value)
 * @method static Builder|\App\LastLocation whereDistance($value)
 * @method static Builder|\App\LastLocation whereId($value)
 * @method static Builder|\App\LastLocation whereLastUpdated($value)
 * @method static Builder|\App\LastLocation whereLatitude($value)
 * @method static Builder|\App\LastLocation whereLongitude($value)
 * @method static Builder|\App\LastLocation whereOdometer($value)
 * @method static Builder|\App\LastLocation whereOffRoad($value)
 * @method static Builder|\App\LastLocation whereOrientation($value)
 * @method static Builder|\App\LastLocation whereReferenceLocationId($value)
 * @method static Builder|\App\LastLocation whereSpeed($value)
 * @method static Builder|\App\LastLocation whereSpeeding($value)
 * @method static Builder|\App\LastLocation whereStatus($value)
 * @method static Builder|\App\LastLocation whereVehicleId($value)
 * @method static Builder|\App\LastLocation whereVehicleStatusId($value)
 * @method static Builder|\App\LastLocation whereVersion($value)
 * @method static Builder|\App\LastLocation whereYesterdayOdometer($value)
 * @mixin Eloquent
 * @property-read Vehicle|null $vehicle
 * @method static Builder|\App\LastLocation newModelQuery()
 * @method static Builder|\App\LastLocation newQuery()
 * @method static Builder|\App\LastLocation query()
 * @property bool|null $vehicle_active
 * @property bool|null $vehicle_in_repair
 * @property int|null $jumps
 * @property int|null $total_locations
 * @property-read Collection|ReportVehicleStatus[] $reportVehicleStatus
 * @method static Builder|\App\LastLocation whereJumps($value)
 * @method static Builder|\App\LastLocation whereTotalLocations($value)
 * @method static Builder|\App\LastLocation whereVehicleActive($value)
 * @method static Builder|\App\LastLocation whereVehicleInRepair($value)
 * @property-read int|null $report_vehicle_status_count
 */
class LastLocation extends Model
{
    const JUMPS_PERCENT_WITH_ISSUES = 40;
    const MINIMUM_LOCATIONS_FOR_ANALYZE_JUMPS = 100;

    protected $dates = ['date'];

    function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
        if(!$date) return Carbon::now();
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function reportVehicleStatus()
    {
        return $this->hasMany(ReportVehicleStatus::class, 'vehicle_id', 'vehicle_id')->where('date', $this->date->toDateString());
    }

    public function getReportVehicleStatus()
    {
        $report = collect([]);
        $reportVehicleStatusAll = $this->reportVehicleStatus;

        foreach ($reportVehicleStatusAll as $reportVehicleStatus ){
            $report->push((object)[
                'status' => $reportVehicleStatus->status,
                'updated_by' => $reportVehicleStatus->updated_by,
            ]);
        }

        return $report->count() ? $report : null;
    }

    public function gpsHasIssues()
    {
        return $this->total_locations > self::MINIMUM_LOCATIONS_FOR_ANALYZE_JUMPS && ($this->total_locations ? 100 * $this->jumps / $this->total_locations : 0) > self::JUMPS_PERCENT_WITH_ISSUES;
    }

    public function gpsIsOK()
    {
        return !$this->gpsHasIssues();
    }
}
