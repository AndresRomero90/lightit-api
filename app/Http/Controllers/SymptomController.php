<?php

namespace App\Http\Controllers;

use App\Services\ApiMedicHealthService;

class SymptomController extends Controller
{
    private $apiMedicHealthService;

    public function __construct(ApiMedicHealthService $apiMedicHealthService)
    {
        $this->apiMedicHealthService = $apiMedicHealthService;
    }

    function index()
    {
        $symptoms = $this->apiMedicHealthService->getSymptoms();

        return response()->json(['symptoms' => $symptoms]);
    }
}
