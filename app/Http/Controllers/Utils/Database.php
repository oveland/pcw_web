<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 10/10/2017
 * Time: 3:52 PM
 */

namespace App\Http\Controllers\Utils;


use Carbon\Carbon;
use DB;

class Database
{
    public static function parseIntervalToTime($interval)
    {
        if ($interval == "") return '00:00:00';
        return DB::select("SELECT parse_interval_to_time('$interval') as parsedTime")[0]->parsedtime;
    }

    public static function addStringTimes($firstStringTime, $secondStringTime)
    {
        return DB::select("SELECT ('$firstStringTime'::INTERVAL + '$secondStringTime'::INTERVAL)::TIME as finalTime")[0]->finaltime;
    }

    public static function findLocationReportModelStringByDate($date)
    {
        $numberSegments = config('maintenance.number_segments');
        $daysPerSegment = config('maintenance.day_per_segment');

        $intervals = collect(range(1, $numberSegments));
        $diff = Carbon::now()->diff(Carbon::parse($date))->days;

        if ($diff == 0) {
            $classLocationReport = "\CurrentLocationReport";
        } else {
            $segmentTarget = $intervals->filter(function ($value, $key) use ($diff, $daysPerSegment) {
                return $value * $daysPerSegment > $diff;
            })->first();
            $classLocationReport = "\LocationReport" . ($segmentTarget ? "$segmentTarget" : "");
        }

        return "App$classLocationReport";
    }

    public static function findLocationReportModelInstanceByDate($date)
    {
        $locationReportModelStringByDate = self::findLocationReportModelStringByDate($date);
        return (new $locationReportModelStringByDate());
    }
}