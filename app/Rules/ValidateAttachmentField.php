<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ValidateAttachmentField implements Rule
{
    protected int $maxSize;
    protected array $allowedExtensions;

    public function __construct(int $maxSize = 10485760, array $allowedExtensions = ['pdf', 'doc', 'jpg', 'jpeg', 'png', 'webp'])
    {
        $this->maxSize = $maxSize;
        $this->allowedExtensions = $allowedExtensions;
    }

    public function passes($attribute, $value)
    {
        if (!$value instanceof UploadedFile) {
            return false;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $size = $value->getSize();

        return in_array($extension, $this->allowedExtensions) && $size <= $this->maxSize;
    }

    public function message()
    {
        return 'The :attribute must be a file of type: ' . implode(', ', $this->allowedExtensions) . ' and may not be greater than ' . ($this->maxSize / 1024 / 1024) . ' MB.';
    }
}
