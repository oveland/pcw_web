<?php

namespace App\Models\Drivers;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
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
 * @property string|null $phone
 * @property string|null $cellphone
 * * @property string|null $address
 * @property string|null $email
 * @property bool|null $active
 * @property int $company_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @mixin Eloquent
 * @method static Builder|Driver whereActive($value)
 * @method static Builder|Driver whereCode($value)
 * @method static Builder|Driver whereCompanyId($value)
 * @method static Builder|Driver whereFirstName($value)
 * @method static Builder|Driver whereId($value)
 * @method static Builder|Driver whereIdentity($value)
 * @method static Builder|Driver whereLastName($value)
 * @method static Builder|Driver whereSecondName($value)
 * @method static Builder|Driver withCode($code)
 * @property int|null $bea_id
 * @property-read mixed $full_name
 * @method static Builder|Driver active()
 * @method static Builder|Driver whereBeaId($value)
 * @property string|null $address
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereCellphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver wherePhone($value)
 */
class Driver extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->second_name $this->last_name" ?? "";
    }

    public function getFullNameAttribute()
    {
        return $this->fullName();
    }

    public function scopeWithCode($query,$code)
    {
        return $query->where('code',$code)->get()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('active',true);
    }

    public function getPhone(){
        return $this->phone . ($this->cellphone ? ", Cel: ".$this->cellphone : "");
    }

    public function infoDetail()
    {
        return __('Identity').": $this->identity\n".__('Phone').": ".$this->getPhone()."\n".__('Email').": $this->email\n".__('Address').": $this->address";
    }
}
