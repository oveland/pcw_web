<?php

use App\Models\BEA\Penalty;
use App\Services\BEA\BEARepository;
use Illuminate\Database\Seeder;

class PenaltiesTableSeeder extends Seeder
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
                'type' => 'boarding',
                'value' => random_int(5, 9) * 100,
            ]
        ];

        foreach ($routes as $index => $route) {
            $c = $criteria[0];
            Penalty::create([
                'route_id' => $route->id,
                'type' => $c->type,
                'value' => $c->value,
            ]);
        }
    }
}
