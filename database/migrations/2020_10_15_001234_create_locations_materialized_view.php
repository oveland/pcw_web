<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsMaterializedView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $params = $this->params();

        foreach (range(1, $params->tables) as $table) {
            $from = $params->days * $table;
            $to = $params->days * ($table - 1) + 1;
            $viewName = "locations_$table";
            $sql = "CREATE MATERIALIZED VIEW $viewName AS (SELECT * FROM locations WHERE date BETWEEN current_date - $from AND current_date - $to) WITH NO DATA";
            dump($sql);
            DB::statement($sql);

            foreach ($params->indexes as $indexColumn) {
                $indexName = $viewName . "_" . $indexColumn . "_index";
                $sqlIndex = "CREATE INDEX $indexName ON $viewName ($indexColumn DESC);";
                DB::statement($sqlIndex);
                dump($sqlIndex);
            }
        }

        $sql = "CREATE TABLE locations_0 AS SELECT * FROM locations WHERE TRUE IS FALSE WITH NO DATA";
        DB::statement($sql);
        foreach ($params->indexes as $indexColumn) {
            $indexName = "locations_0_" . $indexColumn . "_index";
            $sqlIndex = "CREATE INDEX $indexName ON locations_0 ($indexColumn DESC);";
            DB::statement($sqlIndex);
            dump($sqlIndex);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $params = $this->params();

        foreach (range(1, $params->tables) as $table) {
            $viewName = "locations_$table";
            $sql = "DROP MATERIALIZED VIEW IF EXISTS $viewName";
            DB::statement($sql);
            dump($sql);
        }

        $sql = "DROP TABLE IF EXISTS locations_0";
        DB::statement($sql);
        dump($sql);
    }

    public function params()
    {
        return (object)[
            'tables' => 6,
            'days' => 5,
            'indexes' => [
                'date',
                'vehicle_id',
                'dispatch_register_id'
            ]
        ];
    }
}
