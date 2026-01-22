<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NonRemovableSelectField implements ValidationRule
{
    protected string $label;

    public function __construct(string $label = '')
    {
        $this->label = $label;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->label != '') {
            $transformedLabel = $this->label;
        } else {
            $words = explode('_', $attribute);

            $capitalizedWords = array_map('ucwords', $words);

            $transformedLabel = implode(' ', $capitalizedWords);
        }

        if ($value == null) {
            $fail($transformedLabel. ' cannot be empty.');
        }
    }
}
