<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CloseDispatchRegistersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch-registers:close';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'For close or cancel open dispatch registers to the end day';

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
        $query = "
            UPDATE registrodespacho SET cancelado = TRUE, h_reg_cancelado = '23:59:59', observaciones = 'No terminó. Finaliza jornada' 
            WHERE observaciones like '%En camin%' AND (id_empresa = 21 OR id_empresa = 30) AND fecha = current_date - 1 
        ";

        \DB::statement($query);
    }
}
