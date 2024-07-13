<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriversMapResource\Pages;
use App\Filament\Resources\DriversMapResource\RelationManagers;
use App\Models\DriversMap;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DriversMapResource extends Resource
{
    protected static ?string $model = DriversMap::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDriversMaps::route('/'),
            'create' => Pages\CreateDriversMap::route('/create'),
            'edit' => Pages\EditDriversMap::route('/{record}/edit'),
        ];
    }
}
