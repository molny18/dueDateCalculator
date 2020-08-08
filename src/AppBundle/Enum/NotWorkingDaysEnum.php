<?php

declare(strict_types=1);

namespace AppBundle\Enum;

class NotWorkingDaysEnum
{
    public const SATURDAY = 'Sat';

    public const SUNDAY = 'Sun';

    public static function getNotWorkingDays(): array
    {
        return [
            self::SATURDAY,
            self::SUNDAY
        ];
    }
}
