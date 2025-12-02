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
    protected function getAttributeName(string $attribute): string
    {
        $fieldName = last(explode('.', $attribute));

        return trans_choice('validation.attributes.' . $fieldName, 1)
            ?: trans_choice('validation.attributes.' . $attribute, 1)
                ?: str_replace(['_', '.'], ' ', $fieldName);
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        $attributeName = $this->getAttributeName($attribute);
        if (! is_string($value)) {
            $fail(__('email-validation::email-validation.syntax', ['attribute' => $attributeName]));
            return;
        }

        $config = config('email-validation.validations', []);

        if ($config['syntax'] ?? true) {
            if (! $this->service->validateSyntax($value)) {
                $fail(__('email-validation::email-validation.syntax', ['attribute' => $attributeName]));
                return;
            }
        }

        if ($config['disposable'] ?? true) {
            if (! $this->service->validateDisposable($value)) {
                $fail(__('email-validation::email-validation.disposable', ['attribute' => $attributeName]));
                return;
            }
        }

        if ($config['dns'] ?? true) {
            if (! $this->service->validateDns($value)) {
                $fail(__('email-validation::email-validation.dns', ['attribute' => $attributeName]));
                return;
            }
        }
    }
}