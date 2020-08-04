<?php

namespace App\Http\Middleware;

use Closure;
use Dompdf\Exception;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $resolve = $next($request);

        if (method_exists($resolve, 'header')) {
            $resolve
                ->header("Powered-by", "PCW Tech")
                ->header("Access-Control-Allow-Origin", "*")
                ->header("Vary", "Origin")
                ->header("Access-Control-Allow-Methods", "*")
                ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
        }

        return $resolve;
    }
}
