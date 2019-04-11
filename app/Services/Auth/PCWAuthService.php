<?php


namespace App\Services\Auth;

use App\Http\Controllers\GeneralController;
use App\Models\Company\Company;
use Auth;
use Illuminate\Http\Request;

class PCWAuthService
{
    /**
     * @var GeneralController
     */
    private  $generalController;

    public function __construct(GeneralController $generalController)
    {
        $this->generalController = $generalController;
    }

    public function getAccessProperties()
    {
        $user = Auth::user();
        $company = $user->company;

        return (object)[
            'company' => $company,
            'companies' => $user->isAdmin() ? Company::active()->get() : collect([]),
            'drivers' => $company->activeDrivers,
            'routes' => $company->routes,
            'vehicles' => $user->assignedVehicles(null)
        ];
    }

    /**
     * @param Request $request
     * @return Company|null
     */
    public function getCompanyFromRequest(Request $request)
    {
        return $this->generalController->getCompany($request);
    }
}