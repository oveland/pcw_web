<?php

namespace App\Mail;

use App\Models\Company\Company;
use App\Services\Reports\Routes\RouteService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class ConsolidatedReportMail extends Mailable
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
    public $consolidatedReports;
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
    }

    public function setProduction($production = false)
    {
        $this->production = $production;
    }

    public function buildReport()
    {
        $this->consolidatedReports = $this->routeService->consolidated->buildDailyEventsReport($this->company, $this->dateReport);

        return $this->consolidatedReports->sum('totalReports') > 0;
    }

    /**
     * @return Collection
     */
    public function makeFiles()
    {
        return $this->routeService->consolidated->buildDailyEventsReportFiles($this->consolidatedReports, $this->dateReport);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pathToConsolidatesReportFiles = $this->makeFiles();

        $email = $this->view('email.reports.consolidated.daily')->subject(__('Route') . " | " . __('Report events daily') . " | $this->dateReport ".($this->production ? '* Revisado':'> Para revisar'));
        if ($pathToConsolidatesReportFiles->isNotEmpty()) {
            foreach ($pathToConsolidatesReportFiles as $pathToConsolidatesReportFile) {
                $email->attach($pathToConsolidatesReportFile);
            }
        }
        return $email;
    }
}
