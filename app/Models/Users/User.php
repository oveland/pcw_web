<?php

namespace App\Models\Users;

use App\Models\Company\Company;
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
 */
class User extends Authenticatable
{
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

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function isAdmin()
    {
        return $this->company ? $this->company->id === 6 : false;
    }

    public function isSuperAdmin()
    {
        return $this->isAdmin() && ($this->id == 625565 || $this->id == 940736);
    }

    public function isSuperAdmin2()
    {
        return $this->id == 625565;
    }

    public function belongsToTaxcentral()
    {
        return $this->company ? ($this->company->id === 21 || $this->isSuperAdmin()) : false;
    }

    public function belongsToAlameda()
    {
        return $this->company ? ($this->company->id === 14 || $this->isSuperAdmin()) : false;
    }

    public function belongsToCootransol()
    {
        return $this->company ? ($this->company->id === 12 || $this->isSuperAdmin()) : false;
    }

    public function belongsToCompany($company)
    {
        return $this->company->id == $company->id;
    }

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

    public function canAdminGPS()
    {
        return $this->canAdmin();
    }
}
