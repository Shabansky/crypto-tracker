<?php

namespace App\Domain\Shared\Domain;

enum TimeframeHoursEnum: int
{
    case ONE = 1;
    case SIX = 6;
    case TWENTY_FOUR = 24;

    public const VALUES = [1, 6, 24];

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function greatest(): int
    {
        return max(self::values());
    }
}
