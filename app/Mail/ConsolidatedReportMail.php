<?php

namespace App\Mail;

use App\Models\Company\Company;
use App\Services\Reports\ConsolidatedReportsService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsolidatedReportMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var ConsolidatedReportsService
     */
    private $consolidatedReportsService;

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
        $this->consolidatedReportsService = app(ConsolidatedReportsService::class);
    }

    public function buildReport()
    {
        $this->consolidatedReports = $this->consolidatedReportsService->generateConsolidatedReportDaily($this->company, $this->dateReport);

        return $this->consolidatedReports->sum('totalReports') > 0;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pathToConsolidatesReportFiles = $this->consolidatedReportsService->generateConsolidatedReportFiles($this->consolidatedReports);

        $email = $this->view('email.reports.consolidated.daily')->subject(__('Route') . " | " . __('Consolidated report daily') . " | $this->dateReport");
        if ($pathToConsolidatesReportFiles->isNotEmpty()) {
            foreach ($pathToConsolidatesReportFiles as $pathToConsolidatesReportFile) {
                $email->attach($pathToConsolidatesReportFile);
            }
        }
        return $email;
    }
}
