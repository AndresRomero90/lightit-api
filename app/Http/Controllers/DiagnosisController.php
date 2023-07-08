<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiagnosisResource;
use App\Services\ApiMedicHealthService;
use Illuminate\Http\Request;

class DiagnosisController extends Controller
{
    private $apiMedicHealthService;

    public function __construct(ApiMedicHealthService $apiMedicHealthService)
    {
        $this->apiMedicHealthService = $apiMedicHealthService;
    }

    function getDiagnosis(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|array',
            'symptoms.*' => 'integer',
        ]);

        $diagnosis = $this->apiMedicHealthService->getDiagnosis($request->user(), $request->symptoms);

        return response()->json(['diagnosis' => DiagnosisResource::collection($diagnosis)]);
    }
}
