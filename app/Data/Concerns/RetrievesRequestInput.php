<?php

namespace App\Data\Concerns;

use Closure;
use Illuminate\Http\Request;

trait RetrievesRequestInput
{
    protected static function joinFieldNamespace(string $namespace, string $field): string
    {
        return implode('.', array_filter([$namespace, $field]));
    }

    /**
     * @param Request $request
     * @param string $namespace
     * @param 'has'|'input'|'num'|'bool'|'date'|'file' $defaultMethod
     *
     * @return Closure(string $field, 'has'|'input'|'num'|'bool'|'date'|'file' $method=): mixed
     */
    protected static function requestInputGetter(
        Request $request,
        string $namespace = '',
        string $defaultMethod = 'input',
    ): Closure {
        return static function (string $field, ?string $method = null) use ($request, $namespace, $defaultMethod) {
            $method ??= $defaultMethod;
            $method = method_exists($request, $method) ? $method : 'input';

            return [$request, $method](self::joinFieldNamespace($namespace, $field));
        };
    }
}
