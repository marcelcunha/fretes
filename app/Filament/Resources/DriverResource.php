<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use App\Services\IBGEService;
use App\Services\ViaCepService;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getLabel(): string
    {
        return 'Motorista';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Dados Pessoais')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),
                        TextInput::make('cpf')
                            ->label('CPF')
                            ->mask('999.999.999-99')
                            ->rule(['cpf'])
                            ->required()
                            ->unique(ignoreRecord: true),
                    ])->columns(2),
                Section::make('Endereço')
                    ->relationship('address')
                    ->schema([
                        TextInput::make('cep')
                            ->label('CEP')
                            ->mask('99999-999')
                            ->afterStateUpdated(function (?string $state, Set $set, Livewire $livewire) {
                                if (str($state)->length() == 9) {
                                    $result = (new ViaCepService)->findCEP($state);

                                    if (! empty($result)) {
                                        $set('state', data_get($result, 'uf'));
                                        $set('city', data_get($result, 'localidade'));
                                        $set('neighborhood', data_get($result, 'bairro'));
                                        $set('street', data_get($result, 'logradouro'));
                                    } else {
                                        throw ValidationException::withMessages(['cep' => 'CEP não encontrado']);
                                    }
                                } else {
                                    $set('state', null);
                                    self::resetAddressFields($set);
                                }
                            })
                            ->hint(new HtmlString(Blade::render('<x-filament::loading-indicator class="h-5 w-5" />')))
                            ->live()
                            ->columnSpan(2),
                        Select::make('state')
                            ->placeholder('Selecione o estado')
                            ->options(fn () => (new IBGEService)->getStatesArray())
                            ->label('Estado')
                            ->required()
                            ->searchable()
                            ->afterStateUpdated(fn (Set $set) => [
                                self::resetAddressFields($set),
                                $set('cep', null),
                            ])
                            ->live()
                            ->columnSpan(2)
                            ->disabled(fn (Get $get) => ! empty($get('cep')) && strlen($get('cep')) < 9),
                        Select::make('city')
                            ->label('Cidade')
                            ->required()
                            ->searchable(static fn (Select $component) => ! $component->isDisabled())
                            ->disabled(fn (Get $get) => is_null($get('state')))
                            ->options(fn (Get $get) => (new IBGEService)->getCitiesArrayByUF($get('state')))
                            ->live()
                            ->columnSpan(2),
                        TextInput::make('street')
                            ->label('Rua')
                            ->disabled(self::disableFieldsByCEP())
                            ->columnSpan(4),
                        TextInput::make('number')
                            ->label('Número')
                            ->disabled(self::disableFieldsByCEP())->requiredWith('street'),
                        TextInput::make('complement')
                            ->label('Complemento')
                            ->disabled(self::disableFieldsByCEP())
                            ->columnSpan(2),
                        TextInput::make('neighborhood')
                            ->label('Bairro')
                            ->disabled(self::disableFieldsByCEP())
                            ->columnSpan(2),

                    ])->columns(6),
                Section::make('Documentos')
                    // ->relationship('documents')
                    ->schema([
                        FileUpload::make('documents')
                            ->label('Documentos')
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->maxSize(2048)
                            ->multiple(),
                    ])->columns(1),
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
                        return "{$driver->address?->city} - {$driver->address?->state}";
                    })
                    ->searchable()
                    ->sortable(),
            ])->defaultSort('name', 'desc')
            ->filters([
                //
            ])
            ->actions([
                ViewAction::make(),
                Tables\Actions\EditAction::make(),
                DeleteAction::make(),
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
            'view' => Pages\ViewDriver::route('/{record}'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),

        ];
    }

    private static function disableFieldsByCEP(): Closure
    {
        return function (Get $get) {
            $cep = $get('address.cep');

            return is_null($get('address.city')) || ! empty($cep) && strlen($cep) < 9;
        };
    }

    private static function resetAddressFields(Set $set): void
    {
        $set('address.city', null);
        $set('address.neighborhood', null);
        $set('address.street', null);
        $set('address.number', null);
    }
}
