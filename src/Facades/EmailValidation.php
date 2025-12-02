<?php

namespace MohamedSaid\EmailValidation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MohamedSaid\EmailValidation\EmailValidation
 */
class EmailValidation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MohamedSaid\EmailValidation\EmailValidation::class;
    }
}
