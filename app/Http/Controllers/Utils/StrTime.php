<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 28/11/2017
 * Time: 7:53 PM
 */

namespace App\Http\Controllers\Utils;


class StrTime
{
    static function intervalToTime($interval){
        return self::segToStrTime(self::toSeg($interval));
    }

    static function toSeg($strTime)
    {
        if( $strTime == '--:--:--' )return 0;
        $strTimeArray = explode(":", $strTime);

        if(count($strTimeArray) == 2){
            return $strTimeArray[0] * 60 + $strTimeArray[1];
        }
        return $strTimeArray[0] * 3600 + $strTimeArray[1] * 60 + $strTimeArray[2];

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

    static function addStrTime($strTimeA, $strTimeB)
    {
        return self::segToStrTime(self::toSeg($strTimeA) + self::toSeg($strTimeB));
    }

    static function subStrTime($strTimeA, $strTimeB)
    {
        return self::segToStrTime(self::toSeg($strTimeA) - self::toSeg($strTimeB));
    }

    static function toString($time){
        return explode('.',$time)[0];
    }

    static function toShortString($time){
        $fields = explode(':',self::toString($time));
        return "$fields[0]:$fields[1]";
    }
}