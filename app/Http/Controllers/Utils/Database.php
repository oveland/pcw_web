<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 10/10/2017
 * Time: 3:52 PM
 */

namespace App\Http\Controllers\Utils;


class Database
{
    public static function parseIntervalToTime($interval)
    {
        if ($interval == "") return '00:00:00';
        return \DB::select("SELECT parse_interval_to_time('$interval') as parsedTime")[0]->parsedtime;
    }
}