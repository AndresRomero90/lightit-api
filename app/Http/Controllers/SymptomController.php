<?php

namespace App\Http\Controllers;

use App\Services\ApiMedicHealthService;
use Illuminate\Http\JsonResponse;

class SymptomController extends Controller
{
    private $apiMedicHealthService;

    public function __construct(ApiMedicHealthService $apiMedicHealthService)
    {
        $this->apiMedicHealthService = $apiMedicHealthService;
    }

    function index(): JsonResponse
    {
        $symptoms = $this->apiMedicHealthService->getSymptoms();

        return response()->json(['symptoms' => $symptoms]);
    }
}
