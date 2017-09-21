<?php

namespace App\Models\Passengers;

use App\Company;
use App\Vehicle;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Passengers\PassengerCounterPerDays
 *
 * @property int $id
 * @property int|null $total
 * @property float|null $ipk
 * @property string|null $date
 * @property int|null $vehicle_id
 * @property int|null $company_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDays whereCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDays whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDays whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDays whereIpk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDays whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Passengers\PassengerCounterPerDays whereVehicleId($value)
 * @mixin \Eloquent
 * @property-read \App\Company|null $company
 * @property-read \App\Vehicle|null $vehicle
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
