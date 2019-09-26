<?php

namespace App\Mail;

use App\Models\Company\Company;
use App\Services\Reports\Routes\RouteService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ManagementReportMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var RouteService
     */
    private $routeService;

    /**
     * @var Company
     */
    public $company;

    public $dateReport;
    public $routeReport;
    public $vehicleReport;
    public $completedTurns;
    public $managementReport;
    public $production;


    /**
     * Create a new message instance.
     * @param Company $company
     * @param $dateReport
     */
    public function __construct(Company $company, $dateReport)
    {
        $this->company = $company;
        $this->dateReport = $dateReport;
        $this->routeService = app(RouteService::class);
        $this->production = false;

        $this->routeReport = 'all';
        $this->vehicleReport = 'all';
        $this->completedTurns = true;
    }

    public function setProduction($production = false)
    {
        $this->production = $production;
    }

    public function buildReport()
    {
        $this->managementReport = $this->routeService->dispatch->buildManagementReport($this->company, $this->dateReport, $this->routeReport, $this->vehicleReport, $this->completedTurns);

        return $this->managementReport->count();
    }

    /**
     * @return Collection
     */
    public function makeFile()
    {
        return $this->routeService->export->exportManagementReport($this->managementReport, $this->dateReport, true);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('email.reports.consolidated.daily')->subject(__('Route') . " | " . __('Management report') . " | $this->dateReport ");
        $email->attach($this->makeFile());
        return $email;
    }
}
