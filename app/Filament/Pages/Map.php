<?php

namespace App\Filament\Pages;

use App\Forms\Components\RangeSlider;
use App\Models\Driver;
use App\Services\GeolocationService;
use App\Services\IBGEService;
use App\Services\ViaCepService;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class Map extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static string $view = 'filament.pages.map';

    protected static ?string $title = 'Mapa';

    protected static ?string $slug = 'mapa';

    public Collection $drivers;

    public array $data = [];

    public bool $loading = false;

    public ?float $latitude = null;

    public ?float $longitude = null;

    public function mount()
    {
        $this->drivers = Driver::query()
            ->with('address')
            ->get();
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('cep')
                    ->label('CEP')
                    ->mask('99999-999')
                    ->afterStateUpdated(function (?string $state, Set $set) {
                        $this->loading = true;

                        if (str($state)->length() == 9) {
                            $result = (new ViaCepService)->findCEP($state);

                            throw_if(empty($result), ValidationException::withMessages(['data.cep' => 'CEP não encontrado']));

                            $set('state', data_get($result, 'uf'));
                            $set('city', data_get($result, 'localidade'));
                            $set('neighborhood', data_get($result, 'bairro'));
                            $set('street', data_get($result, 'logradouro'));
                        } else {
                            $set('state', null);
                            self::resetAddressFields($set);
                        }
                        $this->loading = false;
                    })
                    ->live(),

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
                    ->reactive()

                    ->disabled(fn (Get $get) => (! empty($get('cep')) && strlen($get('cep')) < 9) || $this->loading),
                Select::make('city')
                    ->label('Cidade')
                    ->required()
                    ->searchable(static fn (Select $component) => ! $component->isDisabled())
                    ->disabled(fn (Get $get) => is_null($get('state')) || $this->loading)
                    ->options(fn (Get $get) => (new IBGEService)->getCitiesArrayByUF($get('state')))
                    ->live(),
                TextInput::make('neighborhood')
                    ->label('Bairro')
                    ->disabled(self::disableFieldsByCEP()),
                TextInput::make('street')
                    ->label('Rua')
                    ->disabled(self::disableFieldsByCEP()),
                TextInput::make('number')
                    ->label('Número')
                    ->disabled(self::disableFieldsByCEP())->requiredWith('street'),

                RangeSlider::make('range')
                    ->min(100)
                    ->max(1000)
                    ->step(20)

                    ->label('Raio de busca'),
            ])
            ->statePath('data');
    }

    private static function disableFieldsByCEP(): Closure
    {
        return function (Get $get) {
            $cep = $get('cep');

            return is_null($get('city')) || ! empty($cep) && strlen($cep) < 9;
        };
    }

    private static function resetAddressFields(Set $set): void
    {
        $set('city', null);
        $set('neighborhood', null);
        $set('street', null);
        $set('number', null);
    }

    public function findCoordinates(GeolocationService $service)
    {
        $this->form->validate();
        $coordinates = $service->coordinates(...$this->data);

        $this->latitude = data_get($coordinates, 'latitude');
        $this->longitude = data_get($coordinates, 'longitude');
    }
}
