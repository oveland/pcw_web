<?php

namespace App\Mail;

use App\Models\Company\Company;
use App\Services\Reports\Passengers\PassengersService as PassengersReporter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConsolidatedPassengersReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var PassengersReporter
     */
    private $passengersReporter;

    /**
     * @var Company
     */
    public $company;
    public $dateReport;
    public $consolidatedReports;

    public $pathToConsolidatesReportFile;


    /**
     * Create a new message instance.
     * @param Company $company
     * @param $dateReport
     */
    public function __construct(Company $company, $dateReport)
    {
        $this->company = $company;
        $this->dateReport = $dateReport;
        $this->passengersReporter = app(PassengersReporter::class);
    }

    public function buildReport()
    {
        $this->consolidatedReports = $this->passengersReporter->consolidated->buildDailyReport($this->company, $this->dateReport);
        $this->pathToConsolidatesReportFile = $this->passengersReporter->consolidated->exportDailyReportFiles($this->consolidatedReports, false);

        return $this->consolidatedReports->totalReports > 0;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->view('email.reports.consolidated.passengers.daily')->subject(__('Passengers') . " | " . __('Consolidated report daily') . " | $this->dateReport");
        if ($this->pathToConsolidatesReportFile) {
            $email->attach($this->pathToConsolidatesReportFile);
        }
        return $email;
    }
}
