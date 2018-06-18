<?php
/**
 * Created by PhpStorm.
 * User: Oscar
 * Date: 4/06/2018
 * Time: 11:05 PM
 */

namespace App\Services\API\Apps\Contracts;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

interface APIInterface
{
    public static function serve(Request $request): JsonResponse;
}