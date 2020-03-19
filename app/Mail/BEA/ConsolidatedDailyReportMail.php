<?php

namespace App\Mail\BEA;

use App\Services\BEA\Reports\BEAReportService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use App\Models\Company\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Exception;
use Storage;

class ConsolidatedDailyReportMail extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var RouteService
     */
    private $reportService;

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
        $this->reportService = app(BEAReportService::class);
        $this->production = false;
    }

    public function setProduction($production = false)
    {
        $this->production = $production;
    }

    public function buildReport()
    {
        $this->consolidatedReports = $this->reportService->buildConsolidatedDailyReport($this->company, $this->dateReport);

        return $this->consolidatedReports->data->count();
    }

    /**
     * @return Collection
     * @throws Exception
     */
    public function makeFiles()
    {
        return $this->reportService->exportConsolidatedDailyReport($this->consolidatedReports, true);
    }

    /**
     * Build the message.
     *
     * @return $this
     * @throws Exception
     */
    public function build()
    {
        $pathToConsolidatesReportFiles = $this->makeFiles();

        $email = $this->view('email.reports.consolidated.daily')->subject(__('BEA') . " | " . __('Consolidated report daily') . " | $this->dateReport ".($this->production ? '✓':''));
        if ($pathToConsolidatesReportFiles->isNotEmpty()) {
            foreach ($pathToConsolidatesReportFiles as $pathToConsolidatesReportFile) {
                $email->attach(Storage::path($pathToConsolidatesReportFile));
            }
        }
        return $email;
    }
}
