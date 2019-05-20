<?php

use App\Models\BEA\Commission;
use App\Services\BEA\BEARepository;
use Illuminate\Database\Seeder;

class CommissionsTableSeeder extends Seeder
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

        $criteria = [
            0 => (object)[
                'type' => 'percent',
                'value' => random_int(5, 10),
            ],
            1 => (object)[
                'type' => 'fixed',
                'value' => random_int(1, 5) * 100,
            ]
        ];

        foreach ($routes as $index => $route) {
            $c = $criteria[random_int(0, 1)];
            Commission::create([
                'route_id' => $route->id,
                'type' => $c->type,
                'value' => $c->value,
            ]);
        }
    }
}
