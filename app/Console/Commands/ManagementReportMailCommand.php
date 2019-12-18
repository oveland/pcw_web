<?php

namespace App\Console\Commands;

use App\Mail\DispatchReportMail;
use App\Mail\ManagementReportMail;
use App\Models\Company\Company;
use App\Models\Users\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Log;
use Mail;

class ManagementReportMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail-routes:management-report {--company=14} {--prev-days=1} {--date=} {--prod=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends management route reports report via email';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        Auth::login(User::find(625565));
    }

    public function __destruct()
    {
        Auth::logout();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $company = Company::find($this->option('company'));

        if ($company) {
            $prevDays = $this->option('prev-days');

            if ($this->option('date')) $dateReport = $this->option('date');
            else $dateReport = Carbon::now()->subDay($prevDays)->toDateString();

            $this->logData("MANAGEMENT REPORT ROUTE DAILY: $company->name > $dateReport");

            $mail = new ManagementReportMail($company, $dateReport);
            if ($mail->buildReport()) {
                $mail->setProduction($this->option('prod'));
                $mailTo = $this->getMailToFromCompany($company, $this->option('prod'));

                $rta = Mail::to($mailTo)->send($mail);

                foreach ($mailTo as $to) {
                    $this->logData("   >> To: $to");
                }
            } else {
                $this->logData("No management route reports found for date $dateReport", 'error');
            }
        } else {
            $this->logData("No company found for id " . $this->option('company'), 'error');
        }
    }

    public function logData($message, $level = 'info')
    {
        $message = "MANAGEMENT REPORT ROUTE > $message";
        $this->info($message);
        switch ($level) {
            case 'warning':
                Log::warning($message);
                break;
            case 'error':
                Log::error($message);
                break;
            default:
                Log::info($message);
                break;
        }
    }

    /**
     * @param Company $company
     * @param bool $production
     * @return array
     */
    public function getMailToFromCompany(Company $company, $production = false)
    {
        $this->logData("Making mail management report route report for '" . ($production ? 'production' : 'development') . "' case...");

        switch ($company->id) {
            /*case Company::ALAMEDA:
                if ($production) {
                    $mailTo = ['oiva.pcw@gmail.com'];
                } else {
                    $mailTo = ['oiva.pcw@gmail.com'];
                }
                break;*/
            case Company::TUPAL:
                if ($production) {
                    $mailTo = ['Diegomanrique1970@gmail.com', 'olatorre22@hotmail.com', 'oscarivelan@gmail.com'];
                } else {
                    $mailTo = ['oiva.pcw@gmail.com', 'soportenivel2pcwtecnologia@outlook.com'];
                    $mailTo = ['oiva.pcw@gmail.com'];
                }
                break;
            case Company::MONTEBELLO:
                if ($production) {
                    $mailTo = ['vivianarodriguez@transmontebello.com', 'olatorre22@hotmail.com', 'oscarivelan@gmail.com'];
                } else {
                    $mailTo = ['oiva.pcw@gmail.com'];
                }
                break;
            default:
                if ($production) {
                    $mailTo = ['Diegomanrique1970@gmail.com', 'olatorre22@hotmail.com', 'oscarivelan@gmail.com'];
                } else {
                    $mailTo = ['oiva.pcw@gmail.com'];
                }
                break;
        }

        return $mailTo;
    }
}