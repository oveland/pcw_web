<?php

namespace App\Mail;

use App\Models\Company\Company;
use App\Services\Reports\Routes\RouteService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

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
    }

    public function buildReport()
    {
        $this->consolidatedReports = $this->routeService->consolidated->buildDailyReport($this->company, $this->dateReport);

        return $this->consolidatedReports->sum('totalReports') > 0;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function makeFiles()
    {
        return $this->routeService->consolidated->buildDailyReportFiles($this->consolidatedReports);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pathToConsolidatesReportFiles = $this->makeFiles();

        $email = $this->view('email.reports.consolidated.daily')->subject(__('Route') . " | " . __('Consolidated report daily') . " | $this->dateReport");
        if ($pathToConsolidatesReportFiles->isNotEmpty()) {
            foreach ($pathToConsolidatesReportFiles as $pathToConsolidatesReportFile) {
                $email->attach($pathToConsolidatesReportFile);
            }
        }
        return $email;
    }
}
