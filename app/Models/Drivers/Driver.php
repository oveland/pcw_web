<?php

namespace App\Models\Drivers;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Drivers\Driver
 *
 * @property int $id
 * @property string|null $code
 * @property string|null $identity
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $last_name
 * @property bool|null $active
 * @property int $company_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver withCode($code)
 */
class Driver extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->last_name" ?? "";
    }

    public function scopeWithCode($query,$code)
    {
        return $query->where('code',$code)->get()->first();
    }
}
