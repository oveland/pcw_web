<?php

namespace App\Models\Passengers;

use App\Company;
use App\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\PassengerCounterPerDaySixMonth
 *
 * @property int $id
 * @property int|null $total
 * @property float|null $ipk
 * @property string|null $date
 * @property int|null $vehicle_id
 * @property int|null $company_id
 * @property-read \App\Company|null $company
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereIpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDaySixMonth whereVehicleId($value)
 * @mixin \Eloquent
 */
class PassengerCounterPerDaySixMonth extends Model
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
