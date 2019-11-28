<?php

namespace App\Models\Vehicles;

use App\Models\Company\Company;
use App\Models\Routes\DispatcherVehicle;
use App\Services\Reports\Passengers\SeatDistributionService;
use App\Services\Reports\Passengers\Seats\SeatTopology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * App\Models\Vehicles\Vehicle
 *
 * @property int $id
 * @property string $plate
 * @property string $number
 * @property int $company_id
 * @property bool $active
 * @property bool $in_repair
 * @property int $bea_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\Company\Company $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereInRepair($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle wherePlate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Vehicles\SimGPS $simGPS
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\MaintenanceVehicle[] $maintenance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vehicles\PeakAndPlate[] $peakAndPlate
 * @property-read mixed $number_and_plate
 * @property-read \App\Models\Vehicles\CurrentLocation $currentLocation
 * @property-read \App\Models\Routes\DispatcherVehicle $dispatcherVehicle
 * @property-read \App\Models\Vehicles\GpsVehicle $gpsVehicle
 * @property-read \App\Models\Routes\DispatcherVehicle $dispatcherVehicles
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereBeaId($value)
 * @property-read \App\Models\Vehicles\VehicleSeatDistribution $seatDistribution
 * @property string|null $observations
 * @property int|null $proprietary_id
 * @property int|null $driver_id
 * @property string|null $tags
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereDriverId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereObservations($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereProprietaryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vehicles\Vehicle whereTags($value)
 */
class Vehicle extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', '=', true);
    }

    public function simGPS()
    {
        return $this->hasOne(SimGPS::class);
    }

    public function dispatcherVehicle()
    {
        return $this->hasOne(DispatcherVehicle::class)->where('default', true)->where('active', true);
    }

    public function dispatcherVehicles()
    {
        return $this->hasOne(DispatcherVehicle::class);
    }

    /**
     * @param Company $company
     * @return bool
     */
    public function belongsToCompany($company)
    {
        return $this->company->id == $company->id;
    }

    public function maintenance()
    {
        return $this->hasMany(MaintenanceVehicle::class);
    }

    public function peakAndPlate()
    {
        return $this->hasMany(PeakAndPlate::class);
    }

    public function numberAndPlate()
    {
        return "$this->number | $this->plate";
    }

    public function getNumberAndPlateAttribute()
    {
        return "$this->number <i class='fa fa-hand-o-right'></i> $this->plate";
    }

    public function getAPIFields(CurrentLocation $currentLocation = null)
    {
        return (object)[
            'id' => $this->id,
            'number' => $this->number,
            'plate' => $this->plate,
            'currentStatus' => $currentLocation ? $currentLocation->vehicleStatus->des_status : ''
        ];
    }

    public function hasRecorderCount()
    {
        return $this->company->hasRecorderCounter();
    }

    public function hasSensorRecorderCount()
    {
        return ($this->plate != 'VCK-531');
    }

    public function hasSensorCount()
    {
        return ($this->plate == 'VCK-531');
    }

    public function countAllFromSensorRecorder()
    {
        $plates = collect([
            'VCD-672A',
        ]);
        return $plates->contains($this->plate);
    }

    public function gpsVehicle()
    {
        return $this->hasOne(GpsVehicle::class);
    }

    public function currentLocation()
    {
        return $this->hasOne(CurrentLocation::class);
    }

    /**
     * @return VehicleSeatDistribution | HasOne
     */
    public function seatDistribution()
    {
        return $this->hasOne(VehicleSeatDistribution::class)->with('topology');
    }

    /**
     * @return SeatTopology
     */
    public function seatTopology()
    {
        $seatDistribution = new SeatDistributionService($this->seatDistribution);
        return $seatDistribution->getTopology();
    }
}
