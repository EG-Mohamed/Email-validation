<?php

namespace MohamedSaid\EmailValidation\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
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