<?php

namespace App\Enums;

enum TrackablePriority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case URGENT = 'urgent';

    public static function getAvailablePriorities(): array
    {
        return [
            self::LOW->value,
            self::MEDIUM->value,
            self::HIGH->value,
            self::URGENT->value,
        ];
    }
}
