<?php

namespace App\Utils;

use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Support\Stringable;
use IntlDateFormatter;

const INTL_DATE = 'dd/MM/YYYY';
const INTL_TIME = 'HH:mm:ss';
const INTL_DATETIME = 'dd/MM/YYYY HH:mm:ss';

final class Locale
{
    public static function appName(): string
    {
        return config('app.name');
    }

    /**
     * @param string $root
     * @param list<int|string> $pieces
     * @param array<string, int|string> $query
     *
     * @return string
     */
    public static function url(string $root, array $pieces = [], array $query = []): string
    {
        $pieces = array_map(rawurlencode(...), $pieces);
        $path = implode('/', [$root, ...$pieces]);

        if ($query) {
            $path .= '?'.http_build_query($query);
        }

        return $path;
    }

    /**
     * @param list<int|string> $pieces
     * @param array<string, int|string> $query
     *
     * @return string
     */
    public static function internalUrl(array $pieces = [], array $query = []): string
    {
        return self::url(config('app.internal_url'), $pieces, $query);
    }

    /**
     * @param list<int|string> $pieces
     * @param array<string, int|string> $query
     *
     * @return string
     */
    public static function webUrl(array $pieces = [], array $query = []): string
    {
        return self::url(config('app.web_url'), $pieces, $query);
    }

    /**
     * Format a number to string. Throws on `null` values.
     *
     * @param float $n
     * @param int $precision
     *
     * @return string
     */
    public static function unsafeNum(float $n, int $precision = 2): string
    {
        return number_format($n, $precision, ',', '');
    }

    /**
     * Format a number to string.
     *
     * @param ?float $n
     * @param int $precision
     *
     * @return ?string
     * @phpstan-return ($n is null ? null : string)
     */
    public static function num(?float $n, int $precision = 2): ?string
    {
        if ($n === null) {
            return null;
        }

        return self::unsafeNum($n, $precision);
    }

    /**
     * Format a number to a BRL string.
     *
     * @param ?float $n
     *
     * @return ?string
     * @phpstan-return ($n is null ? null : string)
     */
    public static function brl(?float $n): ?string
    {
        if ($n === null) {
            return null;
        }

        return 'R$ '.self::num($n, 2);
    }

    /**
     * Applies the user timezone to a date.
     *
     * @param DateTime $date
     * @param DateTimeZone|string $tz
     *
     * @return DateTime
     */
    public static function tz(DateTime $date, DateTimeZone|string $tz): DateTime
    {
        if (!$tz instanceof DateTimeZone) {
            $tz = new DateTimeZone($tz);
        }

        return (clone $date)->setTimezone($tz);
    }

    /**
     * Format a date to Stringable.
     *
     * @param DateTime|string|null $date
     * @param string $format
     * @param DateTimeZone|string|null $tz
     *
     * @return Stringable
     */
    public static function date(DateTime|string|null $date, string $format = 'd/m/Y', DateTimeZone|string|null $tz = null): Stringable
    {
        if ($date === null) {
            return str();
        }

        if ($date instanceof DateTime) {
            $date = $tz ? self::tz($date, $tz) : clone $date;
        }
        else {
            try {
                $date = Carbon::parse($date);
                $date = $tz ? self::tz($date, $tz) : $date;
            } catch (Exception) {
                return str();
            }
        }

        return str(Carbon::parse($date)->format($format));
    }

    /**
     * Format a date to Stringable using Intl.
     *
     * @param DateTime|string|null $date
     * @param string $format
     * @param string $locale
     * @param DateTimeZone|string|null $tz
     *
     * @return Stringable
     */
    public static function intlDate(DateTime|string|null $date, string $format = 'YYYY-MM-dd', string $locale = 'pt-BR', DateTimeZone|string|null $tz = null): Stringable
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        if ($date && $tz) {
            $date = self::tz($date, $tz);
        }

        return str(IntlDateFormatter::formatObject($date, $format, $locale) ?: null);
    }

    public static function booleanize(mixed $value, string $true = 'Sim', string $false = 'Não'): string
    {
        return $value ? $true : $false;
    }
}
