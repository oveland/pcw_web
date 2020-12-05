<?php

namespace App\Models\Vehicles;

use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\BEA\ManagementCost;
use App\Models\Company\Company;
use App\Models\Routes\DispatcherVehicle;
use App\Services\Reports\Passengers\SeatDistributionService;
use App\Services\Reports\Passengers\Seats\SeatTopology;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Sofa\Eloquence\Eloquence;
use Sofa\Eloquence\Mappable;

/**
 * App\Models\Vehicles\Vehicle
 *
 * @property int $id
 * @property strin
 * @property string $number
 * @property int $company_id
 * @property bool $active
 * @property bool $in_repair
 * @property int $bea_id
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
 * @mixin Eloquent
 * @property-read SimGPS $simGPS
 * @property-read Collection|MaintenanceVehicle[] $maintenance
 * @property-read Collection|PeakAndPlate[] $peakAndPlate
 * @property-read mixed $number_and_plate
 * @property-read CurrentLocation $currentLocation
 * @property-read DispatcherVehicle $dispatcherVehicle
 * @property-read GpsVehicle $gpsVehicle
 * @property-read DispatcherVehicle $dispatcherVehicles
 * @method static Builder|Vehicle newModelQuery()
 * @method static Builder|Vehicle newQuery()
 * @method static Builder|Vehicle query()
 * @method static Builder|Vehicle whereBeaId($value)
 * @property-read VehicleSeatDistribution $seatDistribution
 * @property string|null $observations
 * @property int|null $proprietary_id
 * @property int|null $driver_id
 * @property string|null $tags
 * @method static Builder|Vehicle whereDriverId($value)
 * @method static Builder|Vehicle whereObservations($value)
 * @method static Builder|Vehicle whereProprietaryId($value)
 * @method static Builder|Vehicle whereTags($value)
 * @property-read Collection|ManagementCost[] $costsBEA
 * @property-read int|null $costs_b_e_a_count
 * @property-read int|null $maintenance_count
 * @property-read int|null $peak_and_plate_count
 * @property string $plate
 * @property-read ConfigProfile|null $configProfile
 * @property-read ProfileSeat $profile_seating
 * @property-read \App\Models\Apps\Rocket\ProfileSeat|null $profileSeat
 */
class Vehicle extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return BelongsTo | Company
     */
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
     * @return HasMany | ManagementCost[]
     */
    public function costsBEA()
    {
        return $this->hasMany(ManagementCost::class);
    }

    /**
     * @return HasOne
     */
    public function configProfile()
    {
        return $this->hasOne(ConfigProfile::class);
    }

    /**
     * @return HasOne | ProfileSeat
     */
    function profileSeat()
    {
        return $this->hasOne(ProfileSeat::class);
    }

    /**
     * @return ProfileSeat
     */
    public function getProfileSeatingAttribute()
    {
        $profileSeat = $this->profileSeat;
        if (!$profileSeat) {
            $profileSeat = new ProfileSeat();
            $profileSeat->vehicle()->associate($this);
            $profileSeat->save();
        }
        return $profileSeat;
    }
}
