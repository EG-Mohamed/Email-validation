<?php

namespace MohamedSaid\EmailValidation\Commands;

use Illuminate\Console\Command;

class EmailValidationCommand extends Command
{
    public $signature = 'email-validation';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
