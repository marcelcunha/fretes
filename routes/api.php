<?php

use App\Http\Controllers\GeolocationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('coordenadas', [GeolocationController::class, 'index'])->name('geolocation.index');
