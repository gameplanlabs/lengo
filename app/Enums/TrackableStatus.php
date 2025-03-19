<?php

namespace App\Enums;

enum TrackableStatus: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case INPROGRESS = 'in-progress';
    case COMPLETED = 'completed';
    case ACHIEVED = 'achieved';
    case PAUSED = 'paused';
    case CANCELLED = 'cancelled';
    case WORKING = 'working';
    case IDEATION = 'ideation';
    case INREVIEW = 'in-review';
    case ARCHIVED = 'archived';

    public static function getAvailableStatuses(): array
    {
        return [
            self::PLANNING->value,
            self::ACTIVE->value,
            self::INPROGRESS->value,
            self::WORKING->value,
            self::COMPLETED->value,
            self::ACHIEVED->value,
            self::PAUSED->value,
            self::CANCELLED->value,
            self::IDEATION->value,
            self::INREVIEW->value,
            self::ARCHIVED->value,
        ];
    }
}
