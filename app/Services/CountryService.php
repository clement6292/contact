<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CountryService
{
    public function getCountries(): Collection
    {
        try {
            $response = Http::withOptions([
                'verify' => false, // Désactiver la vérification SSL (développement uniquement)
                'timeout' => 10,  // Timeout après 10 secondes
            ])->get('https://restcountries.com/v3.1/all');

            if ($response->failed()) {
                Log::error('Échec de la requête à REST Countries API', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return collect([]);
            }

            $countries = collect($response->json())->map(function ($country) {
                return [
                    'name' => $country['name']['common'] ?? 'Inconnu',
                    'flag' => $country['flags']['png'] ?? '',
                    'code' => $country['cca2'] ?? '',
                ];
            })->sortBy('name');

            return $countries;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des pays', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return collect([]);
        }
    }
}