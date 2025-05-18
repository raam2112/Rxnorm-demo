<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RxNormService;

class DrugSearchController extends Controller
{
    protected $rxNorm;

    public function __construct(RxNormService $rxNorm)
    {
        $this->rxNorm = $rxNorm;
    }

    // No Swagger needed for view returning method
    public function showForm()
    {
        return view('drugs.search');
    }

    // No Swagger needed for view returning method
    public function appsearch(Request $request)
    {
        $request->validate(['drug_name' => 'required|string']);
        $drugName = $request->drug_name;
        $results = $this->rxNorm->searchDrugs($drugName);

        return view('drugs.search', [
            'results' => $results,
            'searched' => true,
            'drug_name' => $drugName
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/drugs/search",
     *     summary="Search drugs by name",
     *     tags={"Drugs"},
     *     @OA\Parameter(
     *         name="drug_name",
     *         in="query",
     *         description="Name of the drug to search",
     *         required=true,
     *         @OA\Schema(type="string", example="aspirin")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search results",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="results",
     *                 type="object",
     *                 @OA\Property(
     *                     property="drugs",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="rxcui", type="string", example="12345"),
     *                         @OA\Property(property="name", type="string", example="Aspirin"),
     *                         @OA\Property(
     *                             property="base_names",
     *                             type="array",
     *                             @OA\Items(type="string", example="Acetylsalicylic Acid")
     *                         ),
     *                         @OA\Property(
     *                             property="dose_forms",
     *                             type="array",
     *                             @OA\Items(type="string", example="Tablet")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Missing or invalid parameter",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Parameter 'drug_name' is required.")

     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */

    public function search(Request $request)
    {
        $drugName = $request->query('drug_name');

        if (!$drugName) {
            return response()->json([
                'message' => 'Parameter "drug_name" is required.'
            ], 400);
        }

        $result = $this->rxNorm->searchDrugs($drugName);

        if (isset($result['error'])) {
            return response()->json($result, 500);
        }

        return response()->json($result);
    }
}
