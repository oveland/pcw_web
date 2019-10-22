<?php

namespace App\Models\Proprietaries;

use App\Models\Company\Company;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Proprietaries\Proprietary
 *
 * @property int $id
 * @property int $identity
 * @property string|null $first_name
 * @property string|null $second_name
 * @property string|null $surname
 * @property string|null $second_surname
 * @property string|null $phone
 * @property string|null $cellphone
 * @property string|null $address
 * @property string|null $email
 * @property bool|null $active
 * @property bool|null $passenger_report_via_sms
 * @property string|null $company_id
 * @property-read Collection|ProprietaryVehicle[] $assignedVehicles
 * @property-read Company|null $company
 * @method static Builder|Proprietary whereActive($value)
 * @method static Builder|Proprietary whereAddress($value)
 * @method static Builder|Proprietary whereCellphone($value)
 * @method static Builder|Proprietary whereCompanyId($value)
 * @method static Builder|Proprietary whereEmail($value)
 * @method static Builder|Proprietary whereFirstName($value)
 * @method static Builder|Proprietary whereId($value)
 * @method static Builder|Proprietary wherePassengerReportViaSms($value)
 * @method static Builder|Proprietary wherePhone($value)
 * @method static Builder|Proprietary whereSecondName($value)
 * @method static Builder|Proprietary whereSecondSurname($value)
 * @method static Builder|Proprietary whereSurname($value)
 * @mixin Eloquent
 * @property-read mixed $simple_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereIdentity($value)
 */
class Proprietary extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->second_name $this->surname $this->second_surname";
    }

    public function getSimpleNameAttribute()
    {
        return "$this->first_name $this->surname";
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedVehicles()
    {
        return $this->hasMany(ProprietaryVehicle::class);
    }

    public function getPhone(){
        return $this->phone . ($this->cellphone ? ", Cel: ".$this->cellphone : "");
    }

    public function infoDetail()
    {
        return __('Identity').": $this->identity\n".__('Phone').": ".$this->getPhone()."\n".__('Email').": $this->email\n".__('Address').": $this->address";
    }
}
