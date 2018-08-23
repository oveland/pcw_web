<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Dispatch
 *
 * @property int $id
 * @property string $name
 * @property int $company_id
 * @property bool $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property float $latitude
 * @property float $longitude
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dispatch whereLongitude($value)
 */
class Dispatch extends Model
{
    //
}
