<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Url;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use View;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/logout',
        '/api',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->ajax() && Auth::guest()) {
            return response('Unauthorized', 401);
        }

        View::share('baseMenu',Url::getBaseMenu($request));

        return $next($request);
    }
}
