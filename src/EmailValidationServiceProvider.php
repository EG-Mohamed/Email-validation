<?php

namespace MohamedSaid\EmailValidation;

use Illuminate\Support\Facades\Validator;
use MohamedSaid\EmailValidation\Rules\EmailValidationRule;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EmailValidationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('email-validation')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        Validator::extend('email_validation', function ($attribute, $value, $parameters, $validator) {
            $rule = new EmailValidationRule();
            $passes = true;

            $rule->validate($attribute, $value, function ($message) use (&$passes, $validator, $attribute) {
                $passes = false;
                $validator->errors()->add($attribute, $message);
            });

            return $passes;
        });
    }
}
