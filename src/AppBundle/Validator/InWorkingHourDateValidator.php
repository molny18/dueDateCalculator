<?php

declare(strict_types=1);

namespace AppBundle\Validator;

use AppBundle\Helper\WorkingRangeHelper;
use AppBundle\Enum\NotWorkingDaysEnum;
use AppBundle\Exception\NotInWorkingHourException;
use AppBundle\Exception\NotOnWorkingDayException;

/**
 * @explanation A Class for validating the given date in working hours
 * @throws InWorkingHourDateValidator
 * @package AppBundle\Validator
 */
class InWorkingHourDateValidator
{

    /**
     * @throws NotInWorkingHourException
     * @throws NotOnWorkingDayException
     */
    public function validate(\DateTimeInterface $date): void
    {
        $this->validateWorkingDay($date);

        $this->validateWorkingHour($date);
    }

    /**
     * @param \DateTimeInterface $date
     * @throws NotOnWorkingDayException
     */
    private function validateWorkingDay(\DateTime $date): void
    {
        if(in_array($date->format('D'),NotWorkingDaysEnum::getNotWorkingDays(),true)){
            throw new NotOnWorkingDayException(sprintf('The given date %s is not a working day',$date->format('Y-m-d D')));
        }
    }

    /**
     * @param \DateTimeInterface $date
     * @throws NotInWorkingHourException
     */
    private function validateWorkingHour(\DateTime $date): void
    {
        $firstWorkingHourTimeStamp = WorkingRangeHelper::getDateFirstWorkingHour($date)->getTimestamp();
        $lastWorkingHourTimeStamp =  WorkingRangeHelper::getDateLastWorkingHour($date)->getTimestamp();
        $givenDateTimeStamp = $date->getTimestamp();

        if(
            $firstWorkingHourTimeStamp > $givenDateTimeStamp ||
            $lastWorkingHourTimeStamp < $givenDateTimeStamp
        ){
            throw new NotInWorkingHourException(sprintf('The given date %s is not in the working hour range', $date->format('Y-m-d H:i:s')));
        }
    }

}
