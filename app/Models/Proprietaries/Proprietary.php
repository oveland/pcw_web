<?php

namespace App\Models\Proprietaries;

use App\Models\Company\Company;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Proprietaries\Proprietary
 *
 * @property int $id
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Proprietaries\ProprietaryVehicle[] $assignedVehicles
 * @property-read \App\Models\Company\Company|null $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereCellphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary wherePassengerReportViaSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereSecondSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Proprietaries\Proprietary whereSurname($value)
 * @mixin \Eloquent
 * @property-read mixed $simple_name
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
}
