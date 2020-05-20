<?php

declare(strict_types=1);

namespace App\Utils;

class StringUtils
{
    public static function stringToLength(string $string, int $int): string
    {
        $length = $int - strlen($string);
        if ($length > 0) {
            return $string.str_repeat('*', $length);
        } elseif ($length < 0) {
            return substr($string, 0, $length);
        }

        return $string;
    }
}
