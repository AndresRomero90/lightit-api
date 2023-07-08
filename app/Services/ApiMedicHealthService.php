<?php

namespace App\Services;

use App\Interfaces\ApiMedicHealthServiceInterface;
use App\Services\ApiMedicAuthService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApiMedicHealthService implements ApiMedicHealthServiceInterface
{
    private $healthServiceUri;
    private $apiMedicAuthService;

    public function __construct(ApiMedicAuthService $apiMedicAuthService)
    {
        $this->apiMedicAuthService = $apiMedicAuthService;
        $this->healthServiceUri = config('services.apimedic.health_service_uri');
    }

    private function httpRequest($method, $endpoint, $data = [])
    {
        $token = $this->apiMedicAuthService->getToken();

        return Http::$method($this->healthServiceUri . $endpoint, array_merge(['token' => $token, 'language' => 'en-gb'], $data));
    }

    public function getSymptoms(): array
    {
        // The symptoms cache expiration time is set to 24hs
        // Given that the symptoms are generals and not something
        // that can change frequently, we can opt by caching them.
        $expiration = 1440;

        $symptoms = Cache::remember('symptoms', $expiration, function () {
            return $this->httpRequest('get', '/symptoms')->json();
        });

        return $symptoms;
    }
}
