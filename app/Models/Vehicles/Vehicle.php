<?php

namespace App\Models\Vehicles;

use App\LastLocation;
use App\Models\Apps\Rocket\ConfigProfile;
use App\Models\Apps\Rocket\ProfileSeat;
use App\Models\BEA\ManagementCost;
use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Proprietaries\Proprietary;
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
 * @mixin Eloquent
 * @property-read SimGPS $simGPS
 * @property-read Collection|MaintenanceVehicle[] $maintenance
 * @property-read Collection|PeakAndPlate[] $peakAndPlate
 * @property-read mixed $number_and_plate
 * @property-read CurrentLocation $currentLocation
 * @property-read DispatcherVehicle $dispatcherVehicle
 * @property-read GpsVehicle $gpsVehicle
 * @property-read DispatcherVehicle $dispatcherVehicles
 * @property int|null $bea_id
 * @property int|null $driver_id
 * @property int|null $proprietary_id
 * @method static Builder|Vehicle whereBeaId($value)
 * @property-read VehicleSeatDistribution $seatDistribution
 * @property-read Driver|null $driver
 * @property-read Proprietary|null $proprietary
 * @method static Builder|Vehicle whereDriverId($value)
 * @method static Builder|Vehicle whereObservations($value)
 * @method static Builder|Vehicle whereProprietaryId($value)
 * @property-read CurrentVehicleIssue $currentIssue
 * @property string|null $tags
 * @property boolean|null $process_takings
 * @method static Builder|Vehicle whereTags($value)
 * @property-read Collection|ManagementCost[] $costsBEA
 * @property-read ConfigProfile|null $configProfile
 * @property-read ProfileSeat $profile_seating
 * @property-read ProfileSeat|null $profileSeat
 */
class Vehicle extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    function getToDateTakingsAttribute($date)
    {
        if (!$date) return null;
        return Carbon::createFromFormat(config('app.date_format'), $date)->toDateString();
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

    public function getAPIFields(CurrentLocation $currentLocation = null, $short = false)
    {
        if ($short) {
            return (object)[
                'id' => $this->id,
                'number' => $this->number,
                'plate' => $this->plate,
                'processTakings' => $this->process_takings,
            ];
        }

        $currentLocation = $currentLocation ? $currentLocation : ($this->currentLocation ? $this->currentLocation : null);
        $vehicleStatus = $currentLocation ? $currentLocation->vehicleStatus : null;

        return (object)[
            'id' => $this->id,
            'number' => $this->number,
            'plate' => $this->plate,
            'companyId' => $this->company_id,
            'currentLocation' => $currentLocation ? $currentLocation->getAPIFields() : [],
            'currentStatus' => $currentLocation ? $currentLocation->vehicleStatus->des_status : '',
            'processTakings' => $this->process_takings,
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
        return LastLocation::whereBetween('date', ["$date 00:00:00", "$date 23:59:59"])->where('vehicle_id', $this->id)->first();
    }

    /**
     * @return Driver | BelongsTo
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * @return Proprietary | BelongsTo
     */
    public function proprietary()
    {
        return $this->belongsTo(Proprietary::class);
    }

    /**
     * @return CurrentVehicleIssue | HasOne
     */
    public function currentIssue()
    {
        return $this->hasOne(CurrentVehicleIssue::class);
    }

    /**
     * @param $issueTypeId
     * @return CurrentVehicleIssue
     */
    public function getCurrentIssue($issueTypeId = null)
    {
        $currentIssue = $this->currentIssue;

        if (!$currentIssue) {
            $currentIssue = new CurrentVehicleIssue([
                'vehicle_id' => $this->id,
            ]);

            $currentIssue->issue_type_id = VehicleIssueType::IN;
            $currentIssue->generateUid();
        };

        $currentLocation = $this->currentLocation;
        $dispatchRegister = $currentLocation ? $currentLocation->dispatchRegister : null;

        if ($issueTypeId) $currentIssue->issue_type_id = $issueTypeId;
        $currentIssue->date = Carbon::now();
        $currentIssue->user_id = auth()->user() ? auth()->user()->id : null;
        $currentIssue->dispatch_register_id = $dispatchRegister ? $dispatchRegister->id : null;
        $currentIssue->driver_id = $dispatchRegister && $dispatchRegister->driver ? $dispatchRegister->driver->id : null;

        $currentIssue->save();

        return $currentIssue;
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
    public function getProfileSeating($camera = 'all')
    {
        $profileSeat = $this->profileSeat()->where('camera', $camera)->first();
        if (!$profileSeat) {
            $profileSeat = new ProfileSeat();
            $profileSeat->vehicle()->associate($this);
            $profileSeat->camera = $camera;
            $profileSeat->occupation = [];
            $profileSeat->save();
        }
        return $profileSeat;
    }
}
