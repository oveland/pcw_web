<?php

namespace App\Http\Middleware;

use \App\Models\System\ViewPermission;
use App\Models\Routes\Route;
use Closure;
use Illuminate\Http\Request;

class Permissions
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
        $grant = ViewPermission::includes($request->path());

        if(!$grant) abort(403);

        return $next($request);
    }
}
