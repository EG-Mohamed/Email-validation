<?php

namespace MohamedSaid\EmailValidation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;
use MohamedSaid\EmailValidation\Services\EmailValidatorService;

class EmailValidationRule implements ValidationRule
{
    protected EmailValidatorService $service;

    public function __construct()
    {
        $this->service = app(EmailValidatorService::class);
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('email-validation::email-validation.syntax')->translate();
            return;
        }

        $config = config('email-validation.validations', []);

        $validations = [
            'syntax' => fn() => $this->service->validateSyntax($value),
            'disposable' => fn() => $this->service->validateDisposable($value),
            'dns' => fn() => $this->service->validateDns($value),
        ];

        foreach ($validations as $key => $validation) {
            if (($config[$key] ?? true) && ! $validation()) {
                $fail("email-validation::email-validation.{$key}")->translate();
                return;
            }
        }
    }
}