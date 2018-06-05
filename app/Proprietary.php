<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Proprietary
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
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ProprietaryVehicle[] $assignedVehicles
 * @property-read \App\Company|null $company
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereCellphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary wherePassengerReportViaSms($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereSecondName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereSecondSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Proprietary whereSurname($value)
 * @mixin \Eloquent
 */
class Proprietary extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->second_name $this->surname $this->second_surname";
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
