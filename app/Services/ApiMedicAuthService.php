<?php

namespace App\Services;

use App\Interfaces\ApiMedicAuthServiceInterface;
use App\Models\ApiMedicToken;
use Carbon\Carbon;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class ApiMedicAuthService implements ApiMedicAuthServiceInterface
{
    private $authServiceUri;
    private $apiKey;
    private $secretKey;

    public function __construct()
    {
        $this->authServiceUri = config('services.apimedic.auth_service_uri');
        $this->apiKey = config('services.apimedic.api_key');
        $this->secretKey = config('services.apimedic.secret_key');
    }

    public function getHashString(string $uri): string
    {
        $secretBytes = mb_convert_encoding($this->secretKey, 'UTF-8');
        $hmac = hash_hmac('md5', $uri, $secretBytes, true);
        return base64_encode($hmac);
    }

    public function login(): ?ApiMedicToken
    {
        $uri = "$this->authServiceUri/login";
        $computedHashString = $this->getHashString($uri);

        $response = Http::withHeaders([
            'Authorization' => "Bearer $this->apiKey:$computedHashString"
        ])->post($uri);

        if ($response->ok()) {
            $tokenResponse = $response->json();

            $apiMedicToken = ApiMedicToken::create([
                'token' => $tokenResponse['Token'],
                'expires_at' => Carbon::now()->addSeconds($tokenResponse['ValidThrough'])
            ]);

            return $apiMedicToken;
        } else {
            throw new RequestException($response);
        }
    }

    public function getToken(): string
    {
        $token = ApiMedicToken::latest('created_at')->first();

        if (!$token || $token->hasExpired()) {
            $token = $this->login();
        }

        return $token->token;
    }
}
