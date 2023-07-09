<?php

namespace App\Interfaces;

use App\Models\Diagnosis;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ApiMedicHealthServiceInterface
{
    function getSymptoms(): array;
    /**
     * Retrieve a collection of users.
     *
     * @return Collection|Diagnosis[]
     */
    function getDiagnosis(User $user, array $symptoms): Collection;

    function confirmDiagnosis(int $diagnosisId): void;
}
