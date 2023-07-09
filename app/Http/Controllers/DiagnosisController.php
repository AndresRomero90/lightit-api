<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiagnosisResource;
use App\Rules\UnconfirmedDiagnosis;
use App\Services\ApiMedicHealthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiagnosisController extends Controller
{
    private $apiMedicHealthService;

    public function __construct(ApiMedicHealthService $apiMedicHealthService)
    {
        $this->apiMedicHealthService = $apiMedicHealthService;
    }

    function getDiagnosis(Request $request): JsonResponse
    {
        $request->validate([
            'symptoms' => 'required|array',
            'symptoms.*' => 'integer',
        ]);

        $diagnosis = $this->apiMedicHealthService->getDiagnosis($request->user(), $request->symptoms);

        return response()->json(['diagnosis' => DiagnosisResource::collection($diagnosis)]);
    }

    function confirmDiagnosis(Request $request): Response
    {
        $request->validate([
            'diagnosis_id' => [
                'required',
                'exists:diagnosis,id',
                new UnconfirmedDiagnosis
            ]
        ]);

        $this->apiMedicHealthService->confirmDiagnosis($request->diagnosis_id);

        return response()->noContent();
    }
}
