<?php

use App\Http\Controllers\GeolocationController;
use Illuminate\Support\Facades\Route;

Route::get('coordenadas', [GeolocationController::class, 'index'])->name('geolocation.index');
