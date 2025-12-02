<?php

namespace MohamedSaid\EmailValidation;

use MohamedSaid\EmailValidation\Commands\EmailValidationCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EmailValidationServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('email-validation')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_email_validation_table')
            ->hasCommand(EmailValidationCommand::class);
    }
}
