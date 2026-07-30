<?php
namespace GTA\Helpers;

class ValidationHelper
{
    public static function isNonNegativeNumber($value): bool
    {
        return is_numeric($value) && (float)$value >= 0;
    }

    public static function isPositiveNumber($value): bool
    {
        return is_numeric($value) && (float)$value > 0;
    }

    public static function isIntInRange($value, int $min, int $max): bool
    {
        $filtered = filter_var($value, FILTER_VALIDATE_INT);
        return $filtered !== false && $filtered >= $min && $filtered <= $max;
    }

    public static function isValidYear($value): bool
    {
        return self::isIntInRange($value, 1900, (int)date('Y') + 1);
    }
}
