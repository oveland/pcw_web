<?php

namespace App\Models\Users;

use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use Auth;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use phpDocumentor\Reflection\Types\This;

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
 */
class User extends Authenticatable
{
    const PROPRIETARY_ROLE = 3;

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

        return in_array( $this->id, $usersCanAdmin ) || $this->isAdmin();

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
        if($onlyReset)return $this->isSuperAdmin();

        $usersCanSendSMS = [
            // VICENTE
            623994798,
            2018101012,
            2018101054,
            523994798,
            423994798,
            2018101065,
        ];

        return (in_array( $this->id, $usersCanSendSMS ) && $this->canAdminGPS()) || $this->isSuperAdmin();
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

    /**
     * @return bool
     */
    public function canViewAllRoutes(){
        return !($this->belongsToMontebello() && $this->isProprietary());
    }

    public function getVehicleTags()
    {
        return collect( $this->vehicle_tags ? explode(',', $this->vehicle_tags) : []);
    }
}
