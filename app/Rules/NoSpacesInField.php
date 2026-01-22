<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoSpacesInField implements ValidationRule
{
    protected bool $isRequired;
    protected ?int $wordLimit;

    public function __construct(bool $isRequired = false, ?int $wordLimit = null)
    {
        $this->isRequired = $isRequired;
        $this->wordLimit = $wordLimit;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->isRequired && is_null($value)) {
            $fail('This field is required.');
            return;
        }

        if (!is_null($value)) {
            $sanitizedValue = trim(strip_tags($value),"&nbsp;");

            if ($sanitizedValue === '') {
                $fail('This field cannot contain only spaces.');
                return;
            }

            if ($this->wordLimit !== null) {
                $wordCount = str_word_count($sanitizedValue);
                if ($wordCount > $this->wordLimit) {
                    $fail("This field cannot contain more than {$this->wordLimit} words.");
                    return;
                }
            }
        }
    }
}
