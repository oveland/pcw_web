<?php

namespace App;

use App\Models\Vehicles\Vehicle;
use App\Models\Vehicles\ReportVehicleStatus;
use Carbon\Carbon;
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereCurrentMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereDateCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereDispatchRegisterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereDistance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereOdometer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereOffRoad($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereOrientation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereReferenceLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereSpeed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereSpeeding($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereVehicleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereVehicleStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LastLocation whereYesterdayOdometer($value)
 * @mixin \Eloquent
 */
class LastLocation extends Model
{
    protected $dates = ['date'];

    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function getDateAttribute($date)
    {
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
}
