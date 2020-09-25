<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Url;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use View;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/logout'
    ];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($request->ajax() && Auth::guest()) {
            return response('Unauthorized', 401);
        }

        View::share('baseMenu', Url::getBaseMenu($request));

        if ($user && !$user->company->active) {
            Session::flash('message', 'Acceso restringido. Porfavor, comuníquese con su empresa o administrador del sistema');
            return abort(403);
        }

        return $next($request);
    }
}
