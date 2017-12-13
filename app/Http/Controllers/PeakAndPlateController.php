<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PeakAndPlateController extends Controller
{
    public function index(Request $request){
        return view('admin.vehicles.peak-and-plate.index');
    }
}
