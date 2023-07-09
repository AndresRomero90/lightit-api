<?php

namespace App\Rules;

use App\Models\Diagnosis;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UnconfirmedDiagnosis implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $diagnosis = Diagnosis::find($value);

        $isCaseUnconfirmed = Diagnosis::where('case_id', $diagnosis->case_id)
            ->where('confirmed', true)
            ->get()
            ->isEmpty();

        if (!$isCaseUnconfirmed) {
            $fail('A diagnosis has already been confirmed for these symptoms.');
        }
    }
}
