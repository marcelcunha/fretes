<?php

namespace App\Http\Controllers;

use App\Services\GeolocationService;
use Illuminate\Http\Request;

class GeolocationController extends Controller
{
    public function index(Request $request)
    {
        $service = new GeolocationService();
        ['address' => $address, 'num' => $num, 'city' => $city, 'state' => $state, 'cep' => $cep] = $request->all();

        return $service->coordinates(
            address: $address,
            num: $num,
            city: $city,
            state: $state,
            cep: $cep
        );
    }
}
