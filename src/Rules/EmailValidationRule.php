<?php

namespace MohamedSaid\EmailValidation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use MohamedSaid\EmailValidation\Services\EmailValidatorService;

class EmailValidationRule implements ValidationRule
{
    protected EmailValidatorService $service;

    public function __construct()
    {
        $this->service = new EmailValidatorService;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(__('email-validation::email-validation.syntax', ['attribute' => $attribute]));
            return;
        }

        $config = config('email-validation.validations', []);

        if ($config['syntax'] ?? true) {
            if (! $this->service->validateSyntax($value)) {
                $fail(__('email-validation::email-validation.syntax', ['attribute' => $attribute]));
                return;
            }
        }

        if ($config['disposable'] ?? true) {
            if (! $this->service->validateDisposable($value)) {
                $fail(__('email-validation::email-validation.disposable', ['attribute' => $attribute]));
                return;
            }
        }

        if ($config['dns'] ?? true) {
            if (! $this->service->validateDns($value)) {
                $fail(__('email-validation::email-validation.dns', ['attribute' => $attribute]));
                return;
            }
        }
    }
}