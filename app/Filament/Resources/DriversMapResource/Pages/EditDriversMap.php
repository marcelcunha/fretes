<?php

namespace App\Filament\Resources\DriversMapResource\Pages;

use App\Filament\Resources\DriversMapResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDriversMap extends EditRecord
{
    protected static string $resource = DriversMapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
