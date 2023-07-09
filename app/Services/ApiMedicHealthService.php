<?php

namespace App\Services;

use App\Http\Resources\DiagnosisResource;
use App\Interfaces\ApiMedicHealthServiceInterface;
use App\Models\Diagnosis;
use App\Models\User;
use App\Services\ApiMedicAuthService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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

    public function getDiagnosis(User $user, array $symptoms): Collection
    {
        $gender = $user->gender;
        $year = Carbon::parse($user->date_of_birth)->year;

        $diagnosisResponse = $this->httpRequest('get', '/diagnosis', ['symptoms' => json_encode($symptoms), 'gender' => $gender, 'year_of_birth' => $year])->json();

        $diagnosisCollection = new Collection();

        $lastCase = Diagnosis::latest('case_id')->first();
        $caseId = $lastCase ? $lastCase->case_id + 1 : 1;

        foreach ($diagnosisResponse as $diagnosis) {
            $issue = $diagnosis['Issue'];

            $diagnosisCollection->push(
                Diagnosis::create([
                    'name' => $issue['Name'],
                    'issue_id' => $issue['ID'],
                    'user_id' => $user->id,
                    'case_id' => $caseId,
                    'accuracy' => $issue['Accuracy'],
                    'symptoms' => $symptoms,
                    'confirmed' => false
                ])
            );
        }

        return $diagnosisCollection;
    }

    public function confirmDiagnosis(int $diagnosisId): void
    {
        $diagnosis = Diagnosis::find($diagnosisId);
        $diagnosis->confirmed = true;
        $diagnosis->save();
    }

    public function getCases(User $user): Collection
    {
        $cases = $user->diagnosis()->distinct('case_id')->pluck('case_id')->toArray();

        $diagnosisCases = new Collection();

        foreach ($cases as $case) {
            $diagnosis = Diagnosis::where('case_id', $case)->get();
            $diagnosisCases->push([
                'symptoms' => $diagnosis->pluck('symptoms')->first(),
                'diagnosis' => DiagnosisResource::collection($diagnosis)
            ]);
        }

        return $diagnosisCases;
    }
}
