<?php

namespace App\Facades;

use App\Services\Facades\LogFacadeService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin LogFacadeService
 */
final class Log extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'logFacadeService';
    }
}
