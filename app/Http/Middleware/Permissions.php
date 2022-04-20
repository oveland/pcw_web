<?php

namespace App\Http\Middleware;

use \App\Models\System\ViewPermission;
use App\Models\Routes\Route;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $path = $request->path();

        if (!$this->passUrlPermission($path) || !$this->passAdmin($path) || !$this->passOperation($path)) abort(403);

        return $next($request);
    }

    function passUrlPermission($path)
    {
        return ViewPermission::includes($path);
    }

    function passAdmin($path)
    {
        $checkUrl = Str::startsWith($path, __('url-administration'));
        return ViewPermission::canAdmin() || !$checkUrl;
    }

    function passOperation($path)
    {
        $checkUrl = Str::startsWith($path, __('url-operation'));
        return ViewPermission::canOperation() || !$checkUrl;
    }
}
