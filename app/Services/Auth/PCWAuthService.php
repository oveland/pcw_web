<?php


namespace App\Services\Auth;

use App\Models\Company\Company;
use Auth;

class PCWAuthService
{
    public function getAccessProperties()
    {
        $user = Auth::user();
        $company = $user->company;

        return (object)[
            'company' => $company,
            'companies' => $user->isAdmin() ? Company::active()->get() : collect([]),
            'routes' => $company->routes,
            'vehicles' => $user->assignedVehicles(null)
        ];
    }
}