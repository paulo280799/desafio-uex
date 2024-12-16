<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ViaCepService
{

    public function getAddresses(string $cep): ?array
    {
        $url = "https://viacep.com.br/ws/{$cep}/json/";

        $response = Http::get($url);

        return $response->json();
    }
}
