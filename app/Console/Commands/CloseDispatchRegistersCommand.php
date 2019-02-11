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
        $thresholdMinTotalReports = 80;
        $query = "
            UPDATE registrodespacho SET cancelado = TRUE, observaciones = 'No terminó.<a class=\"masObservaciones\" data-toggle=\"popover\" title=\"\" data-placement=\"top\"  data-content=\"Falsa salida de despacho. DAD\"><i class=\"fa fa-search\"></i></a>'
            WHERE id_registro IN (
              SELECT dr.id
              FROM (
                         SELECT dr.id, count(rp) total_reports
                         from dispatch_registers as dr
                                     INNER JOIN reports as rp ON (rp.dispatch_register_id = dr.id)
                                     INNER JOIN vehicles as vh ON (vh.id = dr.vehicle_id)
                         where dr.date = (current_date - 1)
                           and (vh.company_id = 21 or vh.company_id = 12)
                           and (dr.status = 'Terminó' or dr.status = 'En camino')
                         group by dr.id
                  ) as dr
              where dr.total_reports < $thresholdMinTotalReports
            )
        ";

        \DB::statement($query);
    }
}
