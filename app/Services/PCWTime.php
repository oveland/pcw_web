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

class PCWTime
{
    /**
     * @param Carbon $start
     * @param Carbon $end
     * @return array
     */
    public static function dateRange(Carbon $start, Carbon $end)
    {
        $dates = [];
        while ($end->greaterThan($start)) {
            $dates[] = $start->copy();
            $start->addDay();
        }
        $dates[] = $start->copy();
        return $dates;
    }
}