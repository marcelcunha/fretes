<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class IBGEService
{
    public function __construct()
    {
        Http::macro('ibge', function () {
            return Http::withOptions([
                'curl' => [
                    CURLOPT_SSL_OPTIONS => CURL_SSLVERSION_TLSv1_2,
                ],
            ])
                ->acceptJson()
                ->baseUrl(config('ibge.url'));
        });
    }
    public function getCitiesArrayByUF(?string $uf): array
    {
        $resposta = $this->getCitiesByUf($uf);

        return Arr::mapWithKeys($resposta, function ($city) {
            return [$city['nome'] => $city['nome']];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function getCitiesByUf(?string $uf): array
    {
        if (is_null($uf)) {
            return [];
        }

        return $this->cacheCities($uf);
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatesArray(): array
    {
        $resposta = $this->cacheStates();

        return Arr::mapWithKeys($resposta, function ($state) {
            return [$state['sigla'] => $state['nome']];
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function getStatesAbbreviations(): array
    {
        $resposta = $this->cacheEstados();

        return Arr::mapWithKeys($resposta, function ($estado) {
            return [$estado['sigla'] => $estado['sigla']];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function cacheCities(string $uf): ?array
    {
        // 180 dias - 6 meses

        return Cache::remember("ibge-{$uf}-cidades", 60 * 60 * 24 * 180, function () use ($uf): ?array {
            try {
                return $this->loadCities($uf);
            } catch (\Exception $e) {
                report($e);
            }

            return null;
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function cacheStates(): array
    {
        // 180 dias - 6 meses
        return Cache::remember('ibge-estados', 60 * 60 * 24 * 180, function () {
            try {
                return $this->loadStates();
            } catch (\Exception $e) {
                report($e);
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function loadCities(string $uf): array
    {
        return Http::ibge()
            ->get("/localidades/estados/{$uf}/municipios?orderBy=nome")
            ->throw()
            ->json();
    }

    /**
     * @return array<string, mixed>
     */
    private function loadStates(): array
    {
        return Http::ibge()
            ->get('/localidades/estados?orderBy=nome')
            ->throw()
            ->json();
    }
}
