<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\Medication;

class UserMedicationService
{
    protected $rxNorm;

    public function __construct(RxNormService $rxNorm)
    {
        $this->rxNorm = $rxNorm;
    }

    public function getUserMedicationsWithDetails(User $user)
    {
        return $user->medications->map(function ($med) {
            $details = $this->rxNorm->getRxcuiHistoryStatus($med->rxcui);
            return [
                'rxcui'      => $med->rxcui,
                'name'       => $med->name,
                'baseNames'  => $details['baseNames'] ?? [],
                'doseForms'  => $details['doseForms'] ?? [],
            ];
        });
    }

    public function addMedication(User $user, string $rxcui)
    {
        $details = $this->rxNorm->getRxcuiProperties($rxcui);

        if (!$details || $details->getStatusCode() !== 200) {
            throw new \Exception('Invalid or unknown RxCUI.');
        }

        $searchResults = $this->rxNorm->searchDrugByRxcui($rxcui);

        if (!isset($searchResults['names'][0]['propValue'])) {
            throw new \Exception('Drug name not found for given RxCUI.');
        }

        $drugName = $searchResults['names'][0]['propValue'];

        return $user->medications()->updateOrCreate(
            ['rxcui' => $rxcui],
            ['name' => $drugName]
        );
    }

    public function deleteMedication(User $user, string $rxcui): bool
    {
        $medication = $user->medications()->where('rxcui', $rxcui)->first();

        if (!$medication) {
            return false;
        }

        return $medication->delete();
    }



        public function getUserMedications(User $user)
    {
        return $user->medications->map(function ($med) {
            try {
                $details = $this->rxNorm->getRxcuiHistoryStatus($med->rxcui);
            } catch (\Exception $e) {
                Log::warning('RxNorm fetch failed: ' . $e->getMessage());
                $details = null;
            }

            return [
                'rxcui'     => $med->rxcui,
                'name'      => $med->name,
                'baseNames' => $details['baseNames'] ?? [],
                'doseForms' => $details['doseForms'] ?? [],
            ];
        });
    }

    public function addUserMedication(User $user, string $rxcui): array
    {
        $details = $this->rxNorm->getRxcuiProperties($rxcui);

        if (!$details) {
            throw new \Exception('Invalid RXCUI');
        }

        try {
            $searchResults = $this->rxNorm->searchDrugByRxcui($rxcui);
        } catch (\Exception $e) {
            Log::error('RxNorm search failed: ' . $e->getMessage());
            $searchResults = null;
        }

        $drugName = 'Unknown';
        if (!empty($searchResults) && isset($searchResults['names'][0]['propValue'])) {
            $drugName = $searchResults['names'][0]['propValue'];
        }

        $user->medications()->updateOrCreate(
            ['rxcui' => $rxcui],
            ['name' => $drugName]
        );

        return ['rxcui' => $rxcui, 'name' => $drugName];
    }

    public function deleteUserMedication(User $user, string $rxcui): bool
    {
        return $user->medications()->where('rxcui', $rxcui)->delete() > 0;
    }
}
