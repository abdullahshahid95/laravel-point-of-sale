<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Unit;

class UnitsController extends Controller
{
    public function units()
    {
        $units = Unit::all();

        return json_encode($units);
    }
}
