<?php

namespace MohamedSaid\EmailValidation\Services;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\DNSCheckValidation;
use Egulias\EmailValidator\Validation\RFCValidation;
use Propaganistas\LaravelDisposableEmail\Validation\Indisposable;

class EmailValidatorService
{
    protected EmailValidator $validator;

    public function __construct()
    {
        $this->validator = new EmailValidator;
    }

    public function validate(string $email): array
    {
        $results = [];
        $config = config('email-validation.validations', []);

        if ($config['syntax'] ?? true) {
            $results['syntax'] = $this->validateSyntax($email);
        }

        if ($config['disposable'] ?? true) {
            $results['disposable'] = $this->validateDisposable($email);
        }

        if ($config['dns'] ?? true) {
            $results['dns'] = $this->validateDns($email);
        }

        return $results;
    }

    public function isValid(string $email): bool
    {
        $results = $this->validate($email);

        foreach ($results as $result) {
            if (! $result) {
                return false;
            }
        }

        return true;
    }

    public function getFailures(string $email): array
    {
        $results = $this->validate($email);
        $failures = [];

        foreach ($results as $key => $result) {
            if (! $result) {
                $failures[] = $key;
            }
        }

        return $failures;
    }

    public function validateSyntax(string $email): bool
    {
        return $this->validator->isValid($email, new RFCValidation);
    }

    public function validateDns(string $email): bool
    {
        return $this->validator->isValid($email, new DNSCheckValidation);
    }

    public function validateDisposable(string $email): bool
    {
        $rule = new Indisposable;

        return $rule->passes('email', $email);
    }
}
