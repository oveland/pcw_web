<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 11/04/2018
 * Time: 10:58 PM
 */

namespace App\Services;

use Carbon\Carbon;
use Excel;
use PhpParser\Node\Scalar\String_;

class PCWTime
{
    /**
     * @param Carbon $start
     * @param Carbon $end
     * @param bool $inclusive
     * @param bool $asString
     * @return array
     */
    public static function dateRange(Carbon $start, Carbon $end, $inclusive = true, $asString = false)
    {
        if($inclusive) $end->addDay();
        $dates = [];
        while ($end->greaterThan($start)) {
            $date = $start->copy();
            $dates[] = $asString ? $date->toDateString() : $date;
            $start->addDay();
        }
        return $dates;
    }

    public static function toDateTimeString($dateString)
    {
        return Carbon::createFromFormat(config('app.simple_date_time_format'), explode('.',$dateString)[0])->toDateTimeString();
    }
}