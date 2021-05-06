<?php

namespace App\Http\Controllers\Rocket;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use App\Services\Auth\PCWAuthService;
use Auth;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportPhotosController extends Controller
{
    /**
     * @var GeneralController
     */
    private $auth;

    public function __construct(PCWAuthService $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function index(Request $request)
    {
        if (app()->environment() == 'dev') {
            return redirect('https://ne.pcwserviciosgps.com/reportes/pasajeros/fotos');
        }

        $user = Auth::user();
        $link = config('app.beta_server_url')."/link/reportes/pasajeros/fotos/$user->id"; // TODO: Change when BETA migrated fully to NE domain

        return view('partials.iframe', compact('link'));
    }

    /**
     * @param Request $request
     * @return Factory|View
     */
    public function video(Request $request)
    {
        if (app()->environment() == 'production') {
            return redirect('http://dev.pcwserviciosgps.com/reportes/pasajeros/video');
        }

        return view('partials.iframeTS');
    }
}
