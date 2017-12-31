<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 27/12/2017
 * Time: 12:49 PM
 */

namespace App\Http\Controllers\Utils;


use Illuminate\Http\Request;

class Url
{
    public static function getBaseMenu(Request $request)
    {
        return ($request ? explode('/',$request->getRequestUri())[1] : null) ?? null;
    }
}