<?php

namespace App\Http\Controllers;

use App\Models\Company\Company;
use App\Models\Drivers\Driver;
use App\Models\Proprietaries\Proprietary;
use Auth;
use Illuminate\Http\Request;

class ProprietaryController extends Controller
{

    /**
     * @var GeneralController
     */
    private $generalController;

    public function __construct(GeneralController $generalController)
    {
        $this->generalController = $generalController;
    }

    public function index(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $companies = Company::active()->orderBy('short_name')->get();
        }
        return view('admin.proprietaries.index', compact('companies'));
    }

    public function show(Request $request)
    {
        $company = $this->generalController->getCompany($request);
        $vehicles = $company->vehicles;
        $proprietaries = $company->proprietaries->sortBy('surname');

        return view('admin.proprietaries.list', compact(['proprietaries','vehicles']));
    }
}
