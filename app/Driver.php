<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Driver
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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver withCode($code)
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
