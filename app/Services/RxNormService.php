<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RxNormService
{
    protected $baseUrl = 'https://rxnav.nlm.nih.gov/REST/';

    // TTLs
    const SHORT_TTL = 3600;        // 1 hour
    const MEDIUM_TTL = 21600;      // 6 hours
    const LONG_TTL = 86400;        // 1 day

    protected function getHttpOptions(): array
    {
        return app()->environment('local') ? ['verify' => false] : [];
    }

 
    public function searchDrugs(string $drugName)
    {
        $cacheKey = 'api_drug_search_' . strtolower($drugName);

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($drugName) {
            $options = app()->environment('local') ? ['verify' => false] : [];

            $response = Http::withOptions($options)->get($this->baseUrl . 'drugs.json', [
                'name' => $drugName,
                'tty'  => 'SBD',
            ]);

            if (!$response->ok()) {
                return ['error' => 'Failed to fetch data from RxNorm API.'];
            }

            $drugs = $response->json()['drugGroup']['conceptGroup'] ?? [];

            return collect($drugs)
                ->filter(fn($group) => $group['tty'] === 'SBD')
                ->flatMap(fn($group) => $group['conceptProperties'] ?? [])
                ->take(5)
                ->map(fn($drug) => $this->enrichDrugData($drug))
                ->values()
                ->all();
        });
    }

    protected function enrichDrugData(array $drug): array
    {
        $rxcui = $drug['rxcui'];
        $options = app()->environment('local') ? ['verify' => false] : [];
        $historyUrl = $this->baseUrl . "rxcui/{$rxcui}/history/status.json";

        $history = Http::withOptions($options)->get($historyUrl);

        $baseNames = [];
        $doseForms = [];

        if ($history->ok()) {
            $statusData = $history->json()['rxcuiStatusHistory'] ?? [];

            $baseNames = collect($statusData['ingredientAndStrength'] ?? [])
                ->pluck('baseName')
                ->unique()
                ->values()
                ->all();

            $doseForms = collect($statusData['doseFormGroupConcept'] ?? [])
                ->pluck('doseFormGroupName')
                ->unique()
                ->values()
                ->all();
        }

        return [
            'rxcui' => $rxcui,
            'name' => $drug['name'],
            'base_names' => $baseNames,
            'dose_forms' => $doseForms,
        ];
    }


    public function getRxcuiHistoryStatus(string $rxcui): array
    {
        $cacheKey = 'rxnorm_history_' . $rxcui;

        return Cache::remember($cacheKey, now()->addSeconds(self::LONG_TTL), function () use ($rxcui) {
            try {
                $url = $this->baseUrl . "rxcui/{$rxcui}/history/status.json";
                $response = Http::withOptions($this->getHttpOptions())->get($url);

                if (!$response->ok()) return [];

                $data = $response->json()['rxcuiStatusHistory'] ?? [];

                return [
                    'baseNames' => collect($data['ingredientAndStrength'] ?? [])
                        ->pluck('baseName')->unique()->values()->all(),
                    'doseForms' => collect($data['doseFormGroupConcept'] ?? [])
                        ->pluck('doseFormGroupName')->unique()->values()->all(),
                ];
            } catch (\Exception $e) {
                Log::error('RxNorm getRxcuiHistoryStatus failed', ['error' => $e->getMessage(), 'rxcui' => $rxcui]);
                return [];
            }
        });
    }

    public function searchDrugByRxcui(string $rxcui): array
    {
        $cacheKey = "rxnorm_rxcui_search_{$rxcui}";

        return Cache::remember($cacheKey, now()->addSeconds(self::MEDIUM_TTL), function () use ($rxcui) {
            try {
                $url = $this->baseUrl . "rxcui/{$rxcui}/allProperties.json?prop=names";
                $response = Http::withOptions($this->getHttpOptions())->get($url);

                if (!$response->ok()) {
                    return ['error' => 'Invalid RxCUI or API error.'];
                }

                $data = $response->json();

                return [
                    'rxcui' => $rxcui,
                    'names' => $data['propConceptGroup']['propConcept'] ?? [],
                ];
            } catch (\Exception $e) {
                Log::error('RxNorm searchDrugByRxcui failed', ['error' => $e->getMessage(), 'rxcui' => $rxcui]);
                return ['error' => 'Exception occurred while contacting RxNorm.'];
            }
        });
    }

    public function getRxcuiProperties(string $rxcui)
    {
        $cacheKey = "rxcui_properties_{$rxcui}";

        return Cache::remember($cacheKey, now()->addSeconds(self::LONG_TTL), function () use ($rxcui) {
            try {
                $url = $this->baseUrl . "rxcui/{$rxcui}/properties.json";
                $response = Http::withOptions($this->getHttpOptions())->get($url);

                if (!$response->ok()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error contacting RxNorm API.'
                    ], 500);
                }

                $data = $response->json('properties');

                if (!$data) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid or unknown RXCUI.'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            } catch (\Exception $e) {
                Log::error('RxNorm getRxcuiProperties failed', ['error' => $e->getMessage(), 'rxcui' => $rxcui]);
                return response()->json([
                    'success' => false,
                    'message' => 'Exception occurred during RxNorm API request.'
                ], 500);
            }
        });
    }
}
