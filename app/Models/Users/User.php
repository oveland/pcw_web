<?php

namespace App\Models\Users;

use App\Models\Company\Company;
use App\Models\Vehicles\Vehicle;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int|null $company_id
 * @property-read \App\Models\Company\Company|null $company
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereUsername($value)
 * @mixin \Eloquent
 * @property int $role_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Users\User whereRoleId($value)
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
        return $this->isAdmin() && ($this->id == 625565 || $this->id == 940736);
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
}
