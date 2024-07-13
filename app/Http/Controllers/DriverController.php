<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function map()
    {
        $drivers = Driver::with('address')->get();

        return view('livewire.drivers-map', ['drivers' => $drivers]);
    }
}
