<?php

namespace MohamedSaid\EmailValidation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use MohamedSaid\EmailValidation\Services\EmailValidatorService;

class EmailValidationRule implements ValidationRule
{
    public function __construct(
        protected ?EmailValidatorService $service = null
    ) {
        $this->service ??= app(EmailValidatorService::class);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail($this->getMessage('syntax', $attribute));

            return;
        }

        $config = config('email-validation.validations', []);

        $validations = [
            'syntax' => fn () => $this->service->validateSyntax($value),
            'disposable' => fn () => $this->service->validateDisposable($value),
            'dns' => fn () => $this->service->validateDns($value),
        ];

        foreach ($validations as $key => $validation) {
            if (($config[$key] ?? true) && ! $validation()) {
                $fail($this->getMessage($key, $attribute));

                return;
            }
        }
    }

    protected function getMessage(string $key, string $attribute): string
    {
        return __("email-validation::email-validation.{$key}", [
            'attribute' => $this->getAttributeName($attribute),
        ]);
    }

    protected function getAttributeName(string $attribute): string
    {
        // Extract the last segment (e.g., "email" from "data.email")
        $key = Str::afterLast($attribute, '.');

        // Try to get the translation
        $translated = __("validation.attributes.{$key}");

        // If translation exists, return it; otherwise format the key
        return $translated !== "validation.attributes.{$key}"
            ? $translated
            : Str::replace(['_', '.'], ' ', $key);
    }
}
