<?php

namespace MohamedSaid\EmailValidation;

use MohamedSaid\EmailValidation\Services\EmailValidatorService;

class EmailValidation
{
    protected EmailValidatorService $service;

    public function __construct()
    {
        $this->service = new EmailValidatorService();
    }

    public function validate(string $email): array
    {
        return $this->service->validate($email);
    }

    public function isValid(string $email): bool
    {
        return $this->service->isValid($email);
    }

    public function getFailures(string $email): array
    {
        return $this->service->getFailures($email);
    }
}
