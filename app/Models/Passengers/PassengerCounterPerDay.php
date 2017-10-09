<?php

namespace App\Models\Passengers;

use App\Company;
use App\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\PassengerCounterPerDay
 *
 * @property int $id
 * @property int|null $total
 * @property float|null $ipk
 * @property string|null $date
 * @property int|null $vehicle_id
 * @property int|null $company_id
 * @property-read \App\Company|null $company
 * @property-read \App\Vehicle|null $vehicle
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereIpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDay whereVehicleId($value)
 * @mixin \Eloquent
 */
class PassengerCounterPerDay extends Model
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
