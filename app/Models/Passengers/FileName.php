<?php

namespace App\Models\Passengers;

use App\Models\Routes\DispatchRegister;
use App\Models\Vehicles\Vehicle;
use Illuminate\Database\Eloquent\Model;

class FileName extends Model
{
    protected $table = 'file_names';

    protected $fillable = [
        'file_name',
        'vehicle_id',
        'dispatch_register_id',
        'file_type',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function dispatchRegister(): BelongsTo
    {
        return $this->belongsTo(DispatchRegister::class, 'dispatch_register_id');
    }
}
