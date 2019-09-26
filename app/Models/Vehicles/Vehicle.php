<?php

namespace App\Models\Vehicles;

use App\LastLocation;
use App\Models\Company\Company;
use App\Models\Routes\DispatcherVehicle;
use App\Services\Reports\Passengers\SeatDistributionService;
use App\Services\Reports\Passengers\Seats\SeatTopology;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * App\Models\Vehicles\Vehicle
 *
 * @property int $id
 * @property string $plate
 * @property string $number
 * @property int $company_id
 * @property bool $active
 * @property bool $in_repair
 * @property string $observations
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Company $company
 * @method static Builder|Vehicle active()
 * @method static Builder|Vehicle whereActive($value)
 * @method static Builder|Vehicle whereCompanyId($value)
 * @method static Builder|Vehicle whereCreatedAt($value)
 * @method static Builder|Vehicle whereId($value)
 * @method static Builder|Vehicle whereInRepair($value)
 * @method static Builder|Vehicle whereNumber($value)
 * @method static Builder|Vehicle wherePlate($value)
 * @method static Builder|Vehicle whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read SimGPS $simGPS
 * @property-read Collection|MaintenanceVehicle[] $maintenance
 * @property-read Collection|PeakAndPlate[] $peakAndPlate
 * @property-read mixed $number_and_plate
 * @property-read CurrentLocation $currentLocation
 * @property-read DispatcherVehicle $dispatcherVehicle
 * @property-read GpsVehicle $gpsVehicle
 * @property-read DispatcherVehicle $dispatcherVehicles
 * @property int|null $bea_id
 * @method static Builder|Vehicle whereBeaId($value)
 * @property-read VehicleSeatDistribution $seatDistribution
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


    /**
     * @param string $date
     * @return LastLocation|CurrentLocation|Model|null
     */
    public function lasLocation($date = null)
    {
        if ($date == Carbon::now()->toDateString()) {
            return CurrentLocation::where('vehicle_id', $this->id)->first();
        }
        return LastLocation::where('date', $date)->where('vehicle_id', $this->id)->first();
    }
}
