<?php

namespace App\Console\Commands;

use App\Models\Vehicles\Speeding;
use Illuminate\Console\Command;
use Carbon\Carbon;
use DB;

class GeocodeSpeedingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geocode:speeding {--from= : Start date (YYYY-MM-DD)} {--to= : End date (YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode speeding records for a given date range';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fromDate = $this->option('from');
        $toDate = $this->option('to');

        if (!$fromDate || !$toDate) {
            $this->error('Please provide --from and --to dates.');
            return;
        }

        $this->info("Geocoding speeding records from $fromDate to $toDate...");

        // Find speeding records in the date range
        $query = Speeding::whereBetween('date', ["$fromDate 00:00:00", "$toDate 23:59:59"])
            ->where('speed', '>', 0)
            ->with('addressLocation'); // Eager load to check existence

        $total = $query->count();
        $this->info("Found $total records.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunk(100, function ($speedings) use ($bar) {
            foreach ($speedings as $speeding) {
                // getAddress() will fetch from DB if exists, or API if not, and save it.
                // We pass refresh=false, force=false.
                try {
                    $speeding->getAddress(false, false);
                } catch (\Exception $e) {
                    // Ignore errors to keep processing
                }
                $bar->advance();
            }
            // Sleep slightly to avoid hitting rate limits too hard if many are missing
            usleep(100000); // 0.1s
        });

        $bar->finish();
        $this->info("\nDone!");
    }
}
