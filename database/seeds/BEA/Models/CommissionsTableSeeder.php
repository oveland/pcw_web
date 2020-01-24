<?php

use App\Models\BEA\Commission;
use App\Models\Company\Company;
use App\Services\BEA\BEASyncService;
use Illuminate\Database\Seeder;

class CommissionsTableSeeder extends Seeder
{
    /**
     * @var Company
     */
    private $company;
    /**
     * @var BEASyncService
     */
    private $sync;

    /**
     * DiscountsTableSeeder constructor.
     * @param BEASyncService $sync
     */
    public function __construct(BEASyncService $sync)
    {
        $this->company = Company::find(Company::PAPAGAYO);
        $this->sync = $sync;
        $this->sync->company = $this->company;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $routes = $this->company->activeRoutes;

        $criteria = [
            0 => (object)[
                'type' => 'percent',
                'value' => 15,
            ],
            1 => (object)[
                'type' => 'fixed',
                'value' => 500,
            ]
        ];

        foreach ($routes as $index => $route) {
            $c = $criteria[0];

            $exists = Commission::where('route_id', $route->id)->first();

            if (!$exists) {
                Commission::create([
                    'route_id' => $route->id,
                    'type' => $c->type,
                    'value' => $c->value,
                ]);
            }
        }
    }
}
