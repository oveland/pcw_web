<?php

namespace App\Http\Middleware;

use App\Models\Routes\Route;
use App\Services\Reports\Users\ActivityLogService;
use Closure;
use Illuminate\Http\Request;

class Activity
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        ActivityLogService::log($request);

        return $next($request);
    }
}
