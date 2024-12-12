<?php

namespace App\Utils;

final class Masks
{
    public static function cnpj(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return mask($value, '##.###.###/####-##');
    }

    public static function cpf(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return mask($value, '###.###.###-##');
    }

    public static function phone(?string $countryCode, ?string $number): string
    {
        return implode(' ', array_filter([self::phoneCountry($countryCode), self::phoneNumber($number)]));
    }

    public static function phoneCountry(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return mask($value, '+#######');
    }

    public static function phoneNumber(?string $value, ?string $countryCode = ''): string
    {
        if (!$value) {
            return '';
        }

        if ($countryCode === '55') {
            if (strlen($value) === 11) {
                return mask($value, '(##) #####-####');
            }

            return mask($value, '(##) ####-####');
        }

        return mask($value, '####################');
    }
}
