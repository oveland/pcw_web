<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class RocketController extends Controller
{
    public function runSync5G(Request $request)
    {
        $imeis = [
            '352557104477222',
            '3525571044772234',
            '3525571044772245'
        ];

        $results = [];

        foreach ($imeis as $imei) {
            Artisan::call('sync5G:sync-photos', [
                '--imei' => $imei
            ]);


            $results[$imei] = Artisan::output();
        }

        return response()->json([
            'status' => 'success',
            'results' => $results
        ]);
    }
}
