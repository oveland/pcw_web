<?php

use App\Models\BEA\Penalty;
use App\Models\Company\Company;
use App\Services\BEA\BEARepository;
use Illuminate\Database\Seeder;

class PenaltiesTableSeeder extends Seeder
{
    /**
     * @var BEARepository
     */
    private $repository;

    /**
     * PenaltiesTableSeeder constructor.
     * @param BEARepository $repository
     */
    public function __construct(BEARepository $repository)
    {
        $this->repository = $repository;
        $this->repository->company = Company::find(Company::PAPAGAYO);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $routes = $this->repository->getAllRoutes();
        $vehicles = $this->repository->getAllVehicles();

        $criteria = [
            0 => (object)[
                'type' => 'boarding',
                'value' => random_int(5, 9) * 100,
            ]
        ];


        foreach ($vehicles as  $vehicle) {
            foreach ($routes as $route) {
                $c = $criteria[0];

                $exists = Penalty::where('vehicle_id', $vehicle->id)->where('route_id', $route->id)->first();

                if (!$exists) {
                    Penalty::create([
                        'vehicle_id' => $vehicle->id,
                        'route_id' => $route->id,
                        'type' => $c->type,
                        'value' => $c->value,
                    ]);
                }
            }
        }
    }
}
