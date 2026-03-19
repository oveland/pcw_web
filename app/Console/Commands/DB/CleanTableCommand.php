<?php

namespace App\Console\Commands\DB;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-table {table} {--keep-since=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Safely cleans a table by saving recent data, truncating, and restoring the recent data.';

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
        $table = $this->argument('table');
        $keepSince = $this->option('keep-since');

        if (!$keepSince) {
            // Default: Keep data from the previous month onwards (e.g. if today is March, keep since Feb 1st)
            $keepSince = Carbon::now()->subMonth()->startOfMonth()->toDateString() . ' 00:00:00';
        }

        $this->info("=====================================================");
        $this->info("Starting clean process for table: {$table}");
        $this->info("Keeping records from: {$keepSince} to NOW");
        $this->info("=====================================================");

        if (!$this->confirm("Are you sure you want to TRUNCATE {$table} and only keep data since {$keepSince}? This action is irreversible.")) {
            $this->warn("Operation cancelled by user.");
            return;
        }

        $tempTable = "{$table}_temp_restore";
        $initialDate = Carbon::now();

        try {
            // Step 1: Create temp table with recent data
            $this->info("\n[1/5] Creating temporary table with recent data...");
            DB::statement("DROP TABLE IF EXISTS {$tempTable}");
            $queryCreate = "CREATE TABLE {$tempTable} AS SELECT * FROM {$table} WHERE date >= '{$keepSince}'";
            $this->log("Running: " . $queryCreate);
            DB::statement($queryCreate);

            // Step 2: Verify data exists in temp table
            $this->info("[2/5] Verifying saved data...");
            $count = DB::table($tempTable)->count();
            $this->info("      -> Saved {$count} recent records in {$tempTable}.");

            if ($count === 0) {
                if (!$this->confirm("WARNING: 0 records found since {$keepSince}. If you proceed, the table will be completely empty. Continue?")) {
                    DB::statement("DROP TABLE IF EXISTS {$tempTable}");
                    $this->warn("Operation aborted to prevent total data loss.");
                    return;
                }
            }

            // Step 3 & 4: Transaction (Lock, Truncate, Insert)
            $this->info("[3/5] Truncating original table and restoring recent data...");
            $this->info("      (This might lock the table for a few seconds)");
            
            DB::transaction(function () use ($table, $tempTable, $count) {
                // Lock the table to prevent new inserts during truncate/restore
                DB::statement("LOCK TABLE {$table} IN EXCLUSIVE MODE");

                // Get columns dynamically to ensure INSERT works perfectly
                $columns = $this->getTableColumns($table);

                // Truncate
                $this->log("TRUNCATE TABLE {$table}");
                DB::statement("TRUNCATE TABLE {$table}");

                // Restore
                if ($count > 0) {
                    $this->log("INSERT INTO {$table} SELECT * FROM {$tempTable}");
                    DB::statement("INSERT INTO {$table} {$columns} SELECT * FROM {$tempTable}");
                }
            });

            // Step 5: Clean up
            $this->info("[5/5] Cleaning up temporary tables...");
            DB::statement("DROP TABLE IF EXISTS {$tempTable}");

            $timeTaken = Carbon::now()->diffForHumans($initialDate, true);
            $this->info("\n✅ Process completed successfully in {$timeTaken}!");
            $this->log("Clean process for {$table} finished successfully.");

        } catch (\Exception $e) {
            $this->error("\n❌ ERROR: " . $e->getMessage());
            $this->log("Error cleaning {$table}: " . $e->getMessage());
            
            // Cleanup just in case
            DB::statement("DROP TABLE IF EXISTS {$tempTable}");
            $this->warn("Temporary table dropped. Original table might be in an inconsistent state if it failed mid-transaction.");
        }
    }

    /**
     * Consulta las columnas de una tabla desde la BD. Crea el formato (col1, col2, ..., colN)
     * necesario para realizar un INSERT INTO ...
     *
     * @param $table
     * @return string
     */
    function getTableColumns($table)
    {
        $columns = collect(DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = '$table' ORDER BY ordinal_position"))->pluck('column_name');
        return "(" . $columns->implode(', ') . ")";
    }

    private function log($message)
    {
        Log::info("[CleanTableCommand] " . $message);
    }
}
