<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserMedicationService;

class UserMedicationController extends Controller
{
    protected $medicationService;

    public function __construct(UserMedicationService $medicationService)
    {
        $this->medicationService = $medicationService;
    }

    /**
     * @OA\Get(
     *     path="/api/medications",
     *     summary="Get list of medications tracked by the authenticated user",
     *     tags={"User Api Medications"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user api medications",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="rxcui", type="string", example="12345"),
     *                 @OA\Property(property="name", type="string", example="Aspirin"),
     *                 @OA\Property(property="added_at", type="string", format="date-time", example="2025-05-18T14:25:36Z")
     *             )
     *         )
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

        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $medications = $this->medicationService->getUserMedications($user);

        return response()->json($medications);
    }

    /**
     * @OA\Post(
     *     path="/api/medications",
     *     summary="Add a medication to the authenticated user's list",
     *     tags={"User Api Medications"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rxcui"},
     *             @OA\Property(property="rxcui", type="string", example="12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medication added successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Medication added."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="rxcui", type="string", example="12345"),
     *                 @OA\Property(property="name", type="string", example="Aspirin")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid RXCUI or other error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="RXCUI not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */

    public function store(Request $request)
    {
        $request->validate([
            'rxcui' => ['required', 'string', 'regex:/^\d+$/'],
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        try {
            $result = $this->medicationService->addUserMedication($user, $request->rxcui);
            return response()->json(['message' => 'Medication added.', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/medications/{rxcui}",
     *     summary="Delete a medication from the authenticated user's list",
     *     tags={"User Api Medications"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="rxcui",
     *         in="path",
     *         required=true,
     *         description="The RXCUI identifier of the medication to delete",
     *         @OA\Schema(type="string", example="12345")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Medication deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Medication deleted.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid RXCUI format",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid RXCUI format.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Medication not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Medication not found.")
     *         )
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

        if (!$user) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if (!preg_match('/^\d+$/', $rxcui)) {
            return response()->json(['error' => 'Invalid RXCUI format.'], 400);
        }

        $deleted = $this->medicationService->deleteUserMedication($user, $rxcui);

        if (!$deleted) {
            return response()->json(['error' => 'Medication not found.'], 404);
        }

        return response()->json(['message' => 'Medication deleted.']);
    }
}
