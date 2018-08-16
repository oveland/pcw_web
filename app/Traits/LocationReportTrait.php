<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 18/12/2017
 * Time: 5:45 PM
 */

namespace App\Traits;

use App\Vehicle;

trait LocationReportTrait
{
    protected function getDateFormat()
    {
        return config('app.date_time_format');
    }

    /**
     * Check valid coordinates
     *
     * @return bool
     */
    public function isValid()
    {
        return ($this->latitude != 0 && $this->longitude != 0)?true:false;
    }

    public function getTableName()
    {
        return $this->table;
    }

    public static function calculateMileageFromGroup($locationReport)
    {
        $firstLocationReport =  $locationReport->first();
        $lastLocationReport =  $locationReport->last();
        $totalKm = ($lastLocationReport->odometer - $firstLocationReport->odometer)/1000;

        return $totalKm;
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}