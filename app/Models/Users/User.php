<?php

namespace App\Models\Users;

use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

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
 * @method static Builder|User whereVehicleTags($value)
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Client[] $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Passport\Token[] $tokens
 * @property-read int|null $tokens_count
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
        return $this->isAdmin() && ($this->id == 625565 || $this->id == 940736 || $this->id == 1130648973);
    }

    /**
     * @return bool
     */
    public function isSuperAdmin2()
    {
        return $this->id == 625565;
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
        return $this->company ? ($this->company->id === Company::MONTEBELLO || $this->isSuperAdmin()) : false;
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
            999459,
            841403,
            679396,
            23994798,
            123994798,
            323994798
        ];

        return in_array( $this->id, $usersCanAdmin ) || $this->isAdmin();

    }

    /**
     * @return bool
     */
    public function canAdminGPS()
    {
        return $this->canAdmin();
    }

    /**
     * @return bool
     */
    public function canSelectRouteReport()
    {
        return $this->belongsToMontebello();
    }

    /**
     * @return bool
     */
    public function isProprietary()
    {
        return $this->role_id == self::PROPRIETARY_ROLE;
    }

    /**
     * @param Company $company
     * @param bool $active
     * @return Vehicle|Vehicle[]
     */
    public function assignedVehicles($company, $active = true)
    {
        if ($this->isProprietary() && $this->belongsToMontebello()) {
            $assignedVehicles = Vehicle::whereIn('plate', collect(\DB::select("SELECT placa plate FROM usuario_vehi WHERE usuario = '$this->username'"))->pluck('plate'));
            if( $active ) $assignedVehicles = $assignedVehicles->active();
            $assignedVehicles = $assignedVehicles->get();
        } else {
            $company = $company ? $company : $this->company;
            $assignedVehicles = $active ? $company->activeVehicles : $company->vehicles;
        }

        return $assignedVehicles;
    }

    public function canML($roleName)
    {
        switch ($roleName){
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
}
