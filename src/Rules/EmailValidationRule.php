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
        $this->service = new EmailValidatorService();
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value)) {
            $fail(__('The :attribute must be a valid email address.'));
            return;
        }

        $config = config('email-validation.validations', []);
        $messages = config('email-validation.messages', []);

        if ($config['syntax'] ?? true) {
            if (!$this->service->validateSyntax($value)) {
                $fail($messages['syntax'] ?? __('The :attribute must be a valid email address.'));
                return;
            }
        }

        if ($config['disposable'] ?? true) {
            if (!$this->service->validateDisposable($value)) {
                $fail($messages['disposable'] ?? __('Disposable email addresses are not allowed.'));
                return;
            }
        }

        if ($config['dns'] ?? true) {
            if (!$this->service->validateDns($value)) {
                $fail($messages['dns'] ?? __('The :attribute domain does not have valid MX records.'));
                return;
            }
        }
    }
}
