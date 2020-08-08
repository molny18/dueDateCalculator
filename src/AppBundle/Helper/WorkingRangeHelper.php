<?php

declare(strict_types=1);

namespace AppBundle\Helper;

use AppBundle\Enum\WorkingHoursEnum;

class WorkingRangeHelper
{

    public static function getDateFirstWorkingHour(\DateTime $date): \DateTime
    {
        return (clone $date)->setTime(WorkingHoursEnum::FIRST_WORKING_HOUR, 0, 0, 0);
    }

    public static function getDateLastWorkingHour(\DateTime $date): \DateTime
    {
        return (clone $date)->setTime(WorkingHoursEnum::LAST_WORKING_HOUR, 0, 0, 0);
    }


}
