<?php


namespace App\Services\Operation\Routes;


use App\Exports\Bearing\BearingExport;
use App\Models\Company\Company;

class BearingService
{
    /**
     * @var Company
     */
    public $company;

    public function __construct(Company $company = null)
    {
        $this->company = $company;
    }

    function export($data)
    {
        $exporter = new BearingExport($data);

        return $exporter->download();
    }
}