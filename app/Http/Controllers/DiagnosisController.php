<?php

namespace App\Http\Controllers;

use App\Http\Resources\DiagnosisResource;
use App\Rules\UnconfirmedDiagnosis;
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

    function confirmDiagnosis(Request $request)
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
