<?php

declare(strict_types=1);

namespace AppBundle\Calculator;

use AppBundle\Enum\NotWorkingDaysEnum;
use AppBundle\Enum\WorkingHoursEnum;
use AppBundle\Exception\AbstractNotInWorkingRangeInterface;
use AppBundle\Exception\NotOnWorkingDayException;
use AppBundle\Helper\WorkingRangeHelper;
use AppBundle\Validator\InWorkingHourDateValidator;

class DueDateCalculator
{
    /**
     * @var InWorkingHourDateValidator
     */
    private $inWorkingHourValidator;

    public function __construct(InWorkingHourDateValidator $inWorkingHourValidator)
    {
        $this->inWorkingHourValidator = $inWorkingHourValidator;
    }

    /**
     * @throws AbstractNotInWorkingRangeInterface
     */
    public function calculateDueDate(\DateTime $submitDate, int $turnaroundHours): \DateTime
    {
        $this->inWorkingHourValidator->validate($submitDate);

        return $this->addTurnaroundTimeInRealDays($submitDate, $turnaroundHours);
    }

    protected function addTurnaroundTimeInRealDays(\DateTime $submitDate, int $turnaroundHours): \DateTime
    {
        $dueDate = clone $submitDate;
        $turnaroundTimeInSeconds = 3600 * $turnaroundHours;

        do {
            $endOfDay = WorkingRangeHelper::getDateLastWorkingHour($dueDate);
            $diffInSecondsToEndOfDay = $this->getDiffInSecondsToEndOfDay($dueDate, $endOfDay);
            $this->addingToEndOfDay($dueDate, $diffInSecondsToEndOfDay, $turnaroundTimeInSeconds);
            $turnaroundTimeInSeconds = $this->subtractDiffInSeconds($turnaroundTimeInSeconds, $diffInSecondsToEndOfDay);
            if (0 !== $turnaroundTimeInSeconds) {
                $this->startNewWorkingDay($dueDate);
            }
        } while (0 < $turnaroundTimeInSeconds);

        return $dueDate;
    }

    private function startNewWorkingDay(\DateTime $date): void
    {
        $date->add(
            new \DateInterval('P1D')
        );
        $date->setTime(WorkingHoursEnum::FIRST_WORKING_HOUR, 0, 0, 0);
        try {
            $this->inWorkingHourValidator->validate($date);
        } catch (NotOnWorkingDayException $ex) {
            $this->skipWeekend($date);
        }
    }

    private function skipWeekend(\DateTime $dueDate): void
    {
        do {
            $dueDate->add(
                new \DateInterval('P1D')
            );
        } while (in_array($dueDate->format('D'), NotWorkingDaysEnum::getNotWorkingDays(), true));

    }

    private function getDiffInSecondsToEndOfDay(\DateTime $submitDate, \DateTime $endOfDay): int
    {
        $difference = $submitDate->diff($endOfDay);
        $differenceHours = $difference->h;
        $differenceMins = $difference->i;
        $differenceSeconds = $difference->s;

        //converting To Secs
        $differenceSeconds += ($differenceMins * 60) + ($differenceHours * 3600);

        return $differenceSeconds;
    }

    private function subtractDiffInSeconds(int $turnaroundTimeInSeconds, int $diffinSecondsToEndOfDay): int
    {
        if ($diffinSecondsToEndOfDay > $turnaroundTimeInSeconds) {
            return 0;
        }
        return $turnaroundTimeInSeconds - $diffinSecondsToEndOfDay;
    }

    private function addingToEndOfDay(\DateTime $dueDate, int $diffinSecondsToEndOfDay, int $turnaroundTimeInSeconds): void
    {
        if ($diffinSecondsToEndOfDay < $turnaroundTimeInSeconds) {
            $dueDate->modify(sprintf('+ %d second', $diffinSecondsToEndOfDay));
        } else {
            $dueDate->modify(sprintf('+ %d second', $turnaroundTimeInSeconds));
        }

    }

}
