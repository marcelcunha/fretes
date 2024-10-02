<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class FilamentRangeFieldServiceProvider extends ServiceProvider
{
    public function packageBooted(): void
    {
        FilamentAsset::register([
            Css::make(
                'range-input-component',
                __DIR__.'/../resources/css/filament-forms-range-component.css'
            ),
        ]);
    }
}
