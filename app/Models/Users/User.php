<?php

namespace App\Models\Users;

use App\Models\Company\Company;
use App\Models\System\ViewPermission;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;

/**
 * App\Models\Users\User
 *
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property string $username
 * @property string $password
 * @property string|null $remember_token
 * @property string|null $role
 * @property Collection $permissions
 * @property bool $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $company_id
 * @property-read Company|null $company
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @method static Builder|User whereActive($value)
 * @method static Builder|User whereCompanyId($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereRole($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User whereUsername($value)
 * @mixin Eloquent
 * @property int $role_id
 * @method static Builder|User whereRoleId($value)
 * @property string|null $vehicle_tags
 * @property-read mixed $user_routes
 * @property-read Collection $userRoutes
 * @method static Builder|User whereVehicleTags($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|UserVehicle[] $userVehicles
 */
class User extends Authenticatable
{
    const ADMIN_ROLE = 1;
    const SYSTEM_ROLE = 2;
    const PROPRIETARY_ROLE = 3;
    const DISPATCHER_ROLE = 4;
    const TAKINGS_ROLE = 5;

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * @return BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->company_id === Company::PCW;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->isAdmin() && (
                $this->id == 625565             // OVELAND
                || $this->id == 940736          // OMAR
                //|| $this->id == 1130648973    // BRIAN
                || $this->id == 2018101255      // LEANDRO
                || $this->id == 2018101280      // OLMER
            );
    }

    /**
     * @return bool
     */
    public function isSuperAdmin2()
    {
        return $this->id == 625565 || $this->id == 2018101280;     // OVELAND, OLMER
    }

    /**
     * @return bool
     */
    public function belongsToTaxcentral()
    {
        return $this->company ? ($this->company->id === Company::MONTEBELLO || $this->isSuperAdmin()) : false;
    }

    /**
     * @return bool
     */
    public function belongsToAlameda()
    {
        return $this->company ? ($this->company->id === Company::ALAMEDA || $this->isSuperAdmin()) : false;
    }

    /**
     * @return bool
     */
    public function belongsToCootransol()
    {
        return $this->company ? ($this->company->id === Company::COOTRANSOL || $this->isSuperAdmin()) : false;
    }

    /**
     * @return bool
     */
    public function belongsToMontebello()
    {
        return $this->company ? ($this->company->id === Company::MONTEBELLO || $this->company->id === Company::URBANUS_MONTEBELLO || $this->isSuperAdmin()) : false;
    }

    /**
     * @param $company
     * @return bool
     */
    public function belongsToCompany($company)
    {
        return $this->company->id == $company->id;
    }

    /**
     * @return bool
     */
    public function canAdmin()
    {
        $usersCanAdmin = [
            999459, // CJHONATAN
            841403, // AJHONATAN
            2018101039, // JHONATAN569
            1130648973, // BRIAN,
            23994798,   // SISTEMATUPAL
            123994798,  // JULIANTP
            323994798,  // JULIANYB,

            // VICENTE
            623994798,
            2018101012,
            2018101054,
            523994798,
            423994798,
            2018101065,
            723994798,
            1818275091 // RICARDO
        ];

        return in_array($this->id, $usersCanAdmin) || $this->isAdmin();

    }

    public function canMakeTakings()
    {
        if ($this->company_id == Company::TRANSPUBENZA) {
            return $this->role_id == self::ADMIN_ROLE || $this->permissions->contains(500);
        }

        return $this->role_id == self::ADMIN_ROLE || $this->role_id == self::SYSTEM_ROLE || $this->role_id == self::TAKINGS_ROLE;
    }

    /**
     * @return bool
     */
    public function canAdminGPS()
    {
        return $this->canAdmin();
    }

    public function canSendSMS($onlyReset = false)
    {
        $usersCanSendSMS = [
            // VICENTE
            623994798,
            2018101012,
            2018101054,
            523994798,
            423994798,
            2018101065,
            723994798
        ];

        return (in_array($this->id, $usersCanSendSMS) && $this->canAdminGPS()) || $this->isSuperAdmin();
    }

    /**
     * @return bool
     */
    public function canSelectRouteReport()
    {
        return true;
        //return $this->belongsToMontebello();
    }

    /**
     * @return bool
     */
    public function isProprietary()
    {
        return $this->role_id == self::PROPRIETARY_ROLE;
    }

    /**
     * @return bool
     */
    public function isDispatcher()
    {
        return $this->role_id == self::DISPATCHER_ROLE;
    }

    /**
     * @param Company $company
     * @param bool $active
     * @return Vehicle[]
     */
    public function assignedVehicles($company = null, $active = true)
    {
        if ($this->isProprietary()) {
            $assignedVehicles = Vehicle::whereIn('id', $this->userVehicles->pluck('vehicle_id'));

            if ($active) $assignedVehicles = $assignedVehicles->active();
            $assignedVehicles = $assignedVehicles->get();
        } else {
            $company = $company ? $company : $this->company;
            $assignedVehicles = $active ? $company->activeVehicles : $company->vehicles;
        }

        return $assignedVehicles;
    }

    /**
     * @return bool
     */
    public function canViewAllRoutes()
    {
        return !($this->belongsToMontebello() && $this->isProprietary());
    }

    public function canEditRecorders()
    {
        $usersCan = [
            #ALAMEDA:
            999457, // GERENCIALAMEDA
            98914189, // JEFE OPERATIVO
            2018101214, // JORGEPB
            31580814, // JEFE RRHH
            2018101243, // ALEXANDERAL
            2018101262, // WILSONAL
            67019334, // ANGELICA ANALISTA

            #TRANSPUBENZA
            2018101273, // SIMONTP
            2018100998, // TPSIS Local
        ];

        return in_array($this->id, $usersCan) || $this->isAdmin();
    }

    public function canEditDrivers()
    {
        $usersCan = [
            #ALAMEDA:
            999457, // GERENCIALAMEDA
            98914189, // JEFE OPERATIVO
            2018101214, // JORGEPB
            31580814, // JEFE RRHH
            2018101243, // ALEXANDERAL
            2018101262, // WILSONAL

            #TRANSPUBENZA
            2018101273, // SIMONTP
        ];

        return in_array($this->id, $usersCan) || $this->isAdmin();
    }

    function canEditFields()
    {
        return $this->canEditRecorders() || $this->canEditDrivers();
    }

    public function getVehicleTags()
    {
        return collect($this->vehicle_tags ? explode(',', $this->vehicle_tags) : []);
    }

    /**
     * @param Company $company
     * @return Collection
     */
    public function getUserRoutes(Company $company)
    {
        $userCompany = $this->company;
        if ($this->isAdmin() && $company) {
            $userCompany = $company;
        }

        return collect(DB::select("SELECT * FROM get_user_routes($this->id, $userCompany->id)"));
    }

    /**
     * @return UserVehicle[] | HasMany
     */
    public function userVehicles()
    {
        return $this->hasMany(UserVehicle::class);
    }

    function getPermissionsAttribute()
    {
        return collect(explode(',', $this->attributes['permissions']))->map(function ($p) {
            return intval(trim($p));
        });
    }

    public function toArray($short = false)
    {
        if ($short) {
            return (object)[
                'id' => $this->id,
                'name' => $this->name,
                'username' => $this->username,
            ];
        }

        return collect(parent::toArray())->put('tag', "$this->username • $this->name")->toArray();
    }
}
