<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use App\Services\IBGEService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(
                    'Dados Pessoais'
                )->schema([
                    TextInput::make('name')
                        ->label('Nome')
                        ->required(),
                    TextInput::make('cpf')
                        ->label('CPF')
                        ->required(),
                ])->columns(2),
                Section::make(
                    'Endereço'
                )->schema([
                    Select::make('address.state')
                        ->placeholder('Selecione o estado')
                        ->options(fn () => (new IBGEService)->getStatesArray())
                        ->label('Estado')
                        ->required()
                        ->reactive()
                        ->columnSpan(2),
                    Select::make('address.city')
                        ->label('Cidade')
                        ->required()
                        ->options(function (Get $get) {
                            $state = $get('address.state');
                            dd((new IBGEService)->getCitiesByUf($state));

                            return (new IBGEService)->getCitiesByUf($state);

                        })
                        ->reactive()
                        ->columnSpan(2),
                    TextInput::make('address.zip')
                        ->label('CEP')
                        ->required()->columnSpan(2),
                    TextInput::make('address.street')
                        ->label('Rua')
                        ->required()->columnSpan(3),
                    TextInput::make('address.number')
                        ->label('Número')
                        ->required(),
                    TextInput::make('address.neighborhood')
                        ->label('Bairro')
                        ->required(),
                ])->columns(6),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address.city')
                    ->label('Cidade')
                    ->getStateUsing(function (Driver $driver) {
                        return "{$driver->address->city} - {$driver->address->state}";
                    })
                    ->searchable()
                    ->sortable(),
            ])->defaultSort('name', 'desc')
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
