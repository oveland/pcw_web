<?php

namespace App\Models\Users;

use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use DB;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Laravel\Passport\Client;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Token;

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
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @property string|null $vehicle_tags
 * @property-read mixed $user_routes
 * @property-read Collection $userRoutes
 * @method static Builder|User whereVehicleTags($value)
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection|UserVehicle[] $userVehicles
 * @property-read int|null $user_vehicles_count
 */
class User extends Authenticatable
{
    const ADMIN_ROLE = 1;
    const SYSTEM_ROLE = 2;
    const PROPRIETARY_ROLE = 3;
    const DISPATCHER_ROLE = 4;
    const ANALYST_ML_ROLE = 5;

    use HasApiTokens, Notifiable;

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
    ];

    function getDateFormat()
    {
        return config('app.simple_date_time_format');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
        return $this->company ? $this->company->id === Company::PCW : false;
    }

    /**
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->isAdmin() && (
                $this->id == 625565             // OVELAND
                || $this->id == 940736          // OMAR
                || $this->id == 1130648973      // BRIAN
            );
    }

    /**
     * @return bool
     */
    public function isSuperAdmin2()
    {
        return $this->id == 625565;     // OVELAND
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
        ];

        return in_array($this->id, $usersCanAdmin) || $this->isAdmin();

    }

    public function canMakeTakings()
    {
        return $this->role_id == self::ADMIN_ROLE || $this->role_id == self::SYSTEM_ROLE;
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

    public function canML($roleName)
    {
        switch ($roleName) {
            case 'liquidate':
                return $this->role_id === self::ANALYST_ML_ROLE || ($this->role_id < 3) || $this->isAdmin();
                break;
            case 'takings':
                return $this->role_id === self::ANALYST_ML_ROLE || $this->role_id === self::DISPATCHER_ROLE || ($this->role_id < 3) || $this->isAdmin();
                break;
            case 'takings-list':
                return $this->role_id === self::ANALYST_ML_ROLE || $this->role_id === self::DISPATCHER_ROLE || ($this->role_id < 3) || $this->isAdmin();
                break;
            case 'admin-params':
                return $this->role_id === self::ANALYST_ML_ROLE || ($this->role_id < 3) || $this->isAdmin();
                break;
            default:
                return false;
                break;

        }
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
