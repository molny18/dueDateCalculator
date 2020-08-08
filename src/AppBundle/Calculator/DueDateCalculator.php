<?php

declare(strict_types=1);

namespace AppBundle\Calculator;

use AppBundle\Enum\NotWorkingDaysEnum;
use AppBundle\Enum\WorkingHoursEnum;
use AppBundle\Exception\NotInWorkingHourException;
use AppBundle\Exception\NotOnWorkingDayException;
use AppBundle\Validator\InWorkingHourDateValidator;

class DueDateCalculator
{
    /**
     * @var InWorkingHourDateValidator
     */
    private $inWorkingHourValidator;

    /**
     * In a real project I do it through DI
     */
    public function __construct()
    {
        $this->inWorkingHourValidator = new InWorkingHourDateValidator();
    }

    public function calculateDueDate(\DateTime $submitDate, int $turnaroundHours): \DateTime
    {
        //Validate the submit date is in working hour
        $this->inWorkingHourValidator->validate($submitDate);

        [$days, $hours] = $this->splitForDaysAndHours($turnaroundHours);

        $dueDate = $this->addDaysAndHours($submitDate, $days, $hours);
        try {
            $this->inWorkingHourValidator->validate($dueDate);
        } catch (NotOnWorkingDayException $ex) {
            $this->skipWeekend($dueDate);
        }
        return $dueDate;
    }

    private function splitForDaysAndHours(int $turnaroundHours): array
    {
        $days = (int)floor($turnaroundHours / 8);
        $hours = $turnaroundHours % 8;

        return [$days, $hours];
    }

    private function addDaysAndHours(\DateTime $submitDate, int $days, int $hours): \DateTime
    {
        if (0 === $days) {
            //clone because of date added by reference, and dont hurt the input data
            return (clone $submitDate)->add(new \DateInterval(sprintf('PT%dH', $hours)));
        }
//        \var_dump($submitDate);die;
        return (clone $submitDate)->modify(sprintf('P%dDT%dH', $days, $hours));
    }

    private function skipWeekend(\DateTime $dueDate): void
    {
        do {
            $dueDate->modify('P1D');
        } while (in_array($dueDate->format('D'), NotWorkingDaysEnum::getNotWorkingDays(), true));

    }

}
