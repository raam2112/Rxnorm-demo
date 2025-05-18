<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\UserMedicationService;

class UserMedicationWebController extends Controller
{
    protected $medicationService;

    public function __construct(UserMedicationService $medicationService)
    {
        $this->medicationService = $medicationService;
    }

    /**
     * @OA\Get(
     *     path="/api/user/medications",
     *     tags={"User Web Medications"},
     *     summary="Get all medications for the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of medications"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        $user = Auth::user();
        $userDrugs = $this->medicationService->getUserMedicationsWithDetails($user);

        return view('drugs.index', compact('userDrugs'));
    }

    /**
     * @OA\Post(
     *     path="/api/user/medications",
     *     tags={"User Web Medications"},
     *     summary="Add a medication",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rxcui"},
     *             @OA\Property(property="rxcui", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medication added successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation failed"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'rxcui' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            $this->medicationService->addMedication($user, $request->rxcui);

            return redirect()->route('drugs.index')->with('success', 'Medication added!');
        } catch (\Exception $e) {
            Log::error('Error storing medication', ['message' => $e->getMessage(), 'rxcui' => $request->rxcui]);

            return back()->withErrors(['rxcui' => $e->getMessage()])->withInput();
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/medications/{rxcui}",
     *     tags={"User Web Medications"},
     *     summary="Delete a medication by RxCUI",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="rxcui",
     *         in="path",
     *         description="RxCUI of the drug",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medication deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Medication not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy($rxcui)
    {
        $user = Auth::user();
        $deleted = $this->medicationService->deleteMedication($user, $rxcui);

        return redirect()->route('drugs.index')->with(
            'message',
            $deleted ? 'Medication deleted.' : 'Medication not found.'
        );
    }
}
