<?php

use App\Models\BEA\Discount;
use App\Services\BEA\BEARepository;
use Illuminate\Database\Seeder;

class DiscountsTableSeeder extends Seeder
{
    /**
     * @var BEARepository
     */
    private $repository;

    public function __construct(BEARepository $repository)
    {
        $this->repository = $repository;
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
        $discountTypes = $this->repository->getAllDiscountTypes();

        foreach ($vehicles as $vehicle) {
            foreach ($routes as $route) {
                $travelRoutes = $this->repository->getTrajectoriesByRoute($route->bea_id);
                foreach ($travelRoutes as $travelRoute) {
                    foreach ($discountTypes as $discountType) {
                        Discount::create([
                            'discount_type_id' => $discountType->id,
                            'vehicle_id' => $vehicle->id,
                            'route_id' => $route->id,
                            'trajectory_id' => $travelRoute->id,
                            'value' => $discountType->default + random_int(-1000, 1000)
                        ]);
                    }
                }
            }
        }
    }
}
