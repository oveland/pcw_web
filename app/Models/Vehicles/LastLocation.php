<?php

namespace App\Models\Vehicles;

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
 * @property Carbon|string|null $date
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
 * @property float|null $mileage
 * @property float|null $current_mileage
 * @property float|null $mileage_route
 * @property float|null $current_mileage_route
 * @property float|null $max_mileage
 * @property float|null $max_current_mileage
 * @property bool|null $speeding
 *
 * @property-read float|null $current_mileage_odometer
 * @method static Builder|LastLocation whereCurrentMileage($value)
 * @method static Builder|LastLocation whereDateCreated($value)
 * @method static Builder|LastLocation whereDispatchRegisterId($value)
 * @method static Builder|LastLocation whereDistance($value)
 * @method static Builder|LastLocation whereId($value)
 * @method static Builder|LastLocation whereLastUpdated($value)
 * @method static Builder|LastLocation whereLatitude($value)
 * @method static Builder|LastLocation whereLongitude($value)
 * @method static Builder|LastLocation whereOdometer($value)
 * @method static Builder|LastLocation whereOffRoad($value)
 * @method static Builder|LastLocation whereOrientation($value)
 * @method static Builder|LastLocation whereReferenceLocationId($value)
 * @method static Builder|LastLocation whereSpeed($value)
 * @method static Builder|LastLocation whereSpeeding($value)
 * @method static Builder|LastLocation whereStatus($value)
 * @method static Builder|LastLocation whereVehicleId($value)
 * @method static Builder|LastLocation whereVehicleStatusId($value)
 * @method static Builder|LastLocation whereVersion($value)
 * @method static Builder|LastLocation whereYesterdayOdometer($value)
 * @mixin Eloquent
 * @property bool|null $vehicle_active
 * @property bool|null $vehicle_in_repair
 * @property int|null $jumps
 * @property int|null $total_locations
 * @property-read Collection|ReportVehicleStatus[] $reportVehicleStatus
 * @property-read Vehicle|null $vehicle
 * @method static Builder|LastLocation whereJumps($value)
 * @method static Builder|LastLocation whereTotalLocations($value)
 * @method static Builder|LastLocation whereVehicleActive($value)
 * @method static Builder|LastLocation whereVehicleInRepair($value)
 */
class LastLocation extends Model
{
    const JUMPS_PERCENT_WITH_ISSUES = 40;
    const MINIMUM_LOCATIONS_FOR_ANALYZE_JUMPS = 100;

    protected $dates = ['date'];
    public $timestamps = false;

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.', $date)[0]);
    }

    public function getMileageAttribute()
    {
        return $this->vehicle->company->countMileageByMax() && $this->attributes['max_mileage'] > 0 ? $this->attributes['max_mileage'] : $this->attributes['odometer'];
    }

    public function getCurrentMileageAttribute()
    {
        return $this->vehicle->company->countMileageByMax() && $this->attributes['max_current_mileage'] > 0 ? $this->attributes['max_current_mileage'] : $this->attributes['current_mileage'];
    }

    public function getCurrentMileageOdometerAttribute()
    {
        return $this->attributes['current_mileage'];
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

        foreach ($reportVehicleStatusAll as $reportVehicleStatus) {
            $report->push((object)[
                'status' => $reportVehicleStatus->status,
                'updated_by' => $reportVehicleStatus->updated_by,
            ]);
        }

        return $report->count() ? $report : null;
    }

    public function gpsHasIssues()
    {
        return $this->total_locations > self::MINIMUM_LOCATIONS_FOR_ANALYZE_JUMPS && (100 * $this->jumps / $this->total_locations) > self::JUMPS_PERCENT_WITH_ISSUES;
    }

    public function gpsIsOK()
    {
        return !$this->gpsHasIssues();
    }
}
