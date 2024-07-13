<?php

use App\Http\Controllers\DriverController;
use App\Livewire\DriversMap;
use App\Models\Driver;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('mapa', [DriverController::class, 'map'])->name('drivers-map');
