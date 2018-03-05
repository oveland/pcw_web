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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereIdentity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Driver whereSecondName($value)
 * @mixin \Eloquent
 */
class Driver extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->last_name" ?? "";
    }
}
