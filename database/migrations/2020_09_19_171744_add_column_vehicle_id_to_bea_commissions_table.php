<?php

use App\Models\BEA\Commission;
use App\Models\Company\Company;
use App\Services\BEA\BEARepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnVehicleIdToBeaCommissionsTable extends Migration
{
    /**
     * @var BEARepository
     */
    private $repository;

    public function __construct()
    {
        $this->repository = new BEARepository();

        $this->repository->forCompany(Company::find(Company::COODETRANS)); // TODO: replace for all companies with BEA
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bea_commissions', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            $table->dropUnique(['route_id', 'type']);
            $table->unique(['route_id', 'type', 'vehicle_id']);
        });

        Schema::table('bea_mark_commissions', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            $table->dropUnique(['route_id', 'type', 'mark_id']);
            $table->unique(['route_id', 'type', 'vehicle_id', 'mark_id']);
        });

        DB::statement("UPDATE bea_mark_commissions SET vehicle_id = (SELECT vehicle_id from bea_turns WHERE id = (select turn_id FROM bea_marks where id = mark_id))");

        $this->seed();
    }

    private function seed()
    {
        $routes = $this->repository->getAllRoutes();

        $vehicles = $this->repository->getAllVehicles();

        foreach ($vehicles as $vehicle) {
            foreach ($routes as $index => $route) {
                $exists = Commission::where('route_id', $route->id)->where('vehicle_id', $vehicle->id)->first();
                if (!$exists) {
                    $originalCommission = Commission::where('route_id', $route->id)->where('vehicle_id', null)->first();
                    Commission::create([
                        'route_id' => $route->id,
                        'type' => $originalCommission ? $originalCommission->type : 'boarding',
                        'vehicle_id' => $vehicle->id,
                        'value' => $originalCommission ? $originalCommission->value : 2000,
                    ]);
                }
            }
        }

        DB::statement("DELETE FROM bea_commissions WHERE vehicle_id IS NULL");
    }

    private function seedRollback()
    {
        $commissionsByRoutes = Commission::all()->groupBy('route_id');

        foreach ($commissionsByRoutes as $routeId => $commissionsByRoute) {
            $commissionByRoute = $commissionsByRoute->sortBy('id')->first();
            DB::statement("DELETE FROM bea_commissions WHERE route_id = $routeId AND id <> $commissionByRoute->id");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->seedRollback();

        Schema::table('bea_commissions', function (Blueprint $table) {
            $table->dropUnique(['route_id', 'type', 'vehicle_id']);
            $table->unique(['route_id', 'type']);

            $table->dropColumn('vehicle_id');
        });

        Schema::table('bea_mark_commissions', function (Blueprint $table) {
            $table->dropUnique(['route_id', 'type', 'vehicle_id', 'mark_id']);
            $table->unique(['route_id', 'type', 'mark_id']);

            $table->dropColumn('vehicle_id');
        });
    }
}
