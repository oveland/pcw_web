<?php

namespace App\Models\Drivers;

use DB;
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
 * @property int $bea_id
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
 * @property-read mixed $full_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver active()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereBeaId($value)
 * @property string|null $phone
 * @property string|null $cellphone
 * @property string|null $address
 * @property string|null $email
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereCellphone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Drivers\Driver wherePhone($value)
 */
class Driver extends Model
{
    public function fullName()
    {
        return "$this->first_name $this->last_name" ?? "";
    }

    public function getFullNameAttribute()
    {
        return $this->fullName();
    }

    public function scopeWithCode($query, $code)
    {
        return $query->where('code', $code)->get()->first();
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * @return bool|int|null
     */
    public function saveData()
    {
        $this->last_name = preg_replace('/[\x00-\x1F\x7F]/u', '', $this->last_name);
        $exploded = explode(" ", $this->last_name);

        $response = null;
        list($lastName1, $lastName2) = count($exploded) > 1 ? $exploded : [$this->last_name, ''];
        if ($this->id) {
            $response = DB::update("
                UPDATE conductor 
                SET bea_id = '$this->bea_id',
                    nombre1 = '" . utf8_encode($this->first_name) . "',
                    apellido1 = '$lastName1', 
                    apellido2 = '$lastName2',
                    identidad = '$this->identity',
                    empresa = '$this->company_id',
                    activo = 'TRUE'
                WHERE id_idconductor = $this->id 
            ");
        } else {
            $response = DB::insert("
                INSERT INTO conductor (bea_id, nombre1, apellido1, apellido2, identidad, empresa, activo) 
                VALUES ('$this->bea_id', '" . utf8_encode($this->first_name) . "', '$lastName1', '$lastName2', '$this->identity', '$this->company_id', 'TRUE')
            ");
        }

        return $response;
    }

    function toArray()
    {
        return (object)[
            'id' => $this->id,
            'code' => $this->code,
            'identity' => $this->identity,
            'fullName' => $this->fullName(),
        ];
    }
}
