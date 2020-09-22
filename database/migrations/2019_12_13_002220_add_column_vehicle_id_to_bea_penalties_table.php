<?php

use App\Models\BEA\Penalty;
use App\Models\Company\Company;
use App\Services\BEA\BEARepository;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnVehicleIdToBeaPenaltiesTable extends Migration
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
        Schema::table('bea_penalties', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            $table->unique(['route_id', 'type', 'vehicle_id']);
        });

        Schema::table('bea_mark_penalties', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');

            $table->unique(['route_id', 'type', 'vehicle_id', 'mark_id']);
        });

        $this->seed();
    }

    public function seed()
    {
        DB::statement("DELETE FROM bea_penalties WHERE TRUE");

        $routes = $this->repository->getAllRoutes();
        $vehicles = $this->repository->getAllVehicles();
        foreach ($vehicles as $vehicle) {
            foreach ($routes as $index => $route) {
                $exists = Penalty::where('route_id', $route->id)->where('type', 'boarding')->where('vehicle_id', $vehicle->id)->first();
                if (!$exists) {
                    Penalty::create([
                        'route_id' => $route->id,
                        'type' => 'boarding',
                        'vehicle_id' => $vehicle->id,
                        'value' => 2000,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bea_penalties', function (Blueprint $table) {
            $table->dropColumn('vehicle_id');
        });

        Schema::table('bea_mark_penalties', function (Blueprint $table) {
            $table->dropColumn('vehicle_id');
        });
    }
}
