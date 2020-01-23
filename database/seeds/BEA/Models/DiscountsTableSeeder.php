<?php

use App\Models\BEA\Discount;
use App\Models\Company\Company;
use App\Services\BEA\BEARepository;
use Illuminate\Database\Seeder;

class DiscountsTableSeeder extends Seeder
{
    /**
     * @var BEARepository
     */
    private $repository;

    /**
     * DiscountsTableSeeder constructor.
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
        $discountTypes = $this->repository->getAllDiscountTypes();

        foreach ($vehicles as $vehicle) {
            foreach ($routes as $route) {
                $trajectories = $this->repository->getTrajectoriesByRoute($route->id);
                foreach ($trajectories as $trajectory) {
                    foreach ($discountTypes as $discountType) {
                        $exists = Discount::where('discount_type_id', $discountType->id)
                            ->where('vehicle_id', $vehicle->id)
                            ->where('route_id', $route->id)
                            ->where('trajectory_id', $trajectory->id)
                            ->first();

                        if (!$exists) {
                            Discount::create([
                                'discount_type_id' => $discountType->id,
                                'vehicle_id' => $vehicle->id,
                                'route_id' => $route->id,
                                'trajectory_id' => $trajectory->id,
                                'value' => $discountType->default + random_int(-1000, 1000)
                            ]);
                        }
                    }
                }
            }
        }
    }
}
