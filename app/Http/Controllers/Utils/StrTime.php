<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 28/11/2017
 * Time: 7:53 PM
 */

namespace App\Http\Controllers\Utils;


use Illuminate\Support\Str;

class StrTime
{
    static function intervalToTime($interval)
    {
        return self::segToStrTime(self::toSeg($interval));
    }

    static function toSeg($strTime, $withSign = false)
    {
        if ($strTime == '--:--:--') return 0;
        $strTimeArray = explode(":", $strTime);

        $sign = 1;
        if($withSign) {
            $sign = Str::startsWith($strTime, '-') ? -1 : 1;
        }

        if (count($strTimeArray) == 2) {
            $h = is_numeric($strTimeArray[0]) ? $strTimeArray[0] : 0;
            $m = is_numeric($strTimeArray[1]) ? $strTimeArray[1] : 0;
            return ($h * 60 + $m) * $sign;
        }

        $h = isset($strTimeArray[0]) && is_numeric($strTimeArray[0]) ? $strTimeArray[0] : 0;
        $m = isset($strTimeArray[1]) && is_numeric($strTimeArray[1]) ? $strTimeArray[1] : 0;
        $s = isset($strTimeArray[2]) && is_numeric($strTimeArray[2]) ? $strTimeArray[2] : 0;

        return ($h * 3600 + $m * 60 + $s) * $sign;
    }

    static function segToStrTime($seconds)
    {
        $seconds = abs(intval($seconds));
        $hours = floor($seconds / 3600);
        $horas = ($hours >= 24) ? ($hours - 24) : $hours;

        $minutes = floor(($seconds - ($hours * 3600)) / 60);
        $seconds = $seconds - ($hours * 3600) - ($minutes * 60);

        $srt_hours = $horas < 10 ? "0$hours" : "$hours";
        $srt_minutes = $minutes < 10 ? "0$minutes" : "$minutes";
        $srt_seconds = $seconds < 10 ? "0$seconds" : "$seconds";

        return date('H:i:s', strtotime($srt_hours . ':' . $srt_minutes . ":" . $srt_seconds));
    }

    static function timeAGreaterThanTimeB($strTimeA, $strTimeB)
    {
        if (self::toSeg($strTimeA) - self::toSeg($strTimeB) >= 0) {
            return true;
        } else {
            return false;
        }
    }

    static function difference($strTimeA, $strTimeB)
    {
        if (self::timeAGreaterThanTimeB($strTimeB, $strTimeA)) {
            return "+" . self::subStrTime($strTimeB, $strTimeA);
        } else {
            return "-" . self::subStrTime($strTimeA, $strTimeB);
        }
    }

    static function differenceInSeconds($strTimeA, $strTimeB)
    {
        if (self::timeAGreaterThanTimeB($strTimeB, $strTimeA)) {
            return self::toSeg(self::subStrTime($strTimeB, $strTimeA));
        } else {
            return -self::toSeg(self::subStrTime($strTimeA, $strTimeB));
        }
    }

    static function addStrTime($strTimeA, $strTimeB)
    {
        return self::segToStrTime(self::toSeg($strTimeA) + self::toSeg($strTimeB));
    }

    static function subStrTime($strTimeA, $strTimeB)
    {
        return self::segToStrTime(self::toSeg($strTimeA) - self::toSeg($strTimeB));
    }

    static function toString($time)
    {
        return explode('.', $time)[0];
    }

    static function toShortString($time)
    {
        $fields = explode(':', self::toString($time));
        return "$fields[0]:$fields[1]";
    }
}