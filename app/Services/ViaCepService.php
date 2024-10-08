<?php

namespace App\Services;

use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ViaCepService
{
    private string $url;

    public function __construct()
    {
        $this->url = config('viacep.url');
    }

    /**
     * @return array<string, mixed>
     */
    public function findCEP(string $cep): ?array
    {
        return $this->cacheCEP($cep);
    }

    /**
     * @return array<string, mixed>
     */
    private function cacheCEP(string $cep): ?array
    {
        //1 mês
        return Cache::remember("viacep-{$cep}", 60 * 60 * 24 * 30, function () use ($cep) {
            try {
                return $this->loadCEP($cep);
            } catch (HttpClientException $e) {
            } catch (\Exception $e) {
                report($e);
            }

            return [];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function loadCEP(string $cep): array
    {
        $resultado = Http::acceptJson()
            ->get($this->url."/$cep/json")
            ->throw()
            ->json();

        if (isset($resultado['erro'])) {
            throw new HttpClientException('CEP não encontrado');
        }

        return $resultado;
    }
}
