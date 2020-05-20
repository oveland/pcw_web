<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request)
            ->header("Powered-by", "PCW Tech")
            ->header("Access-Control-Allow-Origin", "*")
            ->header("Vary", "Origin")
            ->header("Access-Control-Allow-Methods", "*")
            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
    }
}
