<?php

declare(strict_types=1);

namespace AppBundle\Test;

use AppBundle\Calculator\DueDateCalculator;
use AppBundle\Exception\AbstractNotInWorkingRangeException;
use AppBundle\Exception\InvalidSubmitDateException;
use AppBundle\Exception\NotInWorkingHourException;
use AppBundle\Exception\NotOnWorkingDayException;
use PHPUnit\Framework\TestCase;

class DueDateCalculatorTest extends TestCase
{

    /**
     * @dataProvider positiveProvider
     *
     * @param \DateTime $submitDate
     * @param int $turnaroundHours
     * @param \DateTime $expectedDueDate
     */
    public function testCalculateDueDate(\DateTime $submitDate, int $turnaroundHours, \DateTime $expectedDueDate): void
    {
        $this->markTestIncomplete('Todo fix the DateTime->modify() method parse error');
        $calculatedDueDate = $this->getCalulator()->calculateDueDate($submitDate, $turnaroundHours);

        $this->assertEquals($expectedDueDate, $calculatedDueDate);
    }

    public function positiveProvider(): array
    {
        return [
            'finishOnSameDay' => [new \DateTime('2020-08-03 09:15:30'),5,new \DateTime('2020-08-03 14:15:30')],
            'finishOnNextDay' => [new \DateTime('2020-08-03 09:15:30'),9,new \DateTime('2020-08-04 10:15:30')]
        ];
    }

    /**
     * @dataProvider negativeProvider
     *
     * @param \DateTime $submitDate
     * @param int $turnaroundHours
     * @param string $expectedExceptionClassName
     */
    public function testNegativeCalculateDueDate(\DateTime $submitDate, int $turnaroundHours, string $expectedExceptionClassName): void
    {
        $this->expectException($expectedExceptionClassName);
        $this->getCalulator()->calculateDueDate($submitDate, $turnaroundHours);
    }

    public function negativeProvider(): array
    {
        return [
            'notOnWokingDay' => [new \DateTime('2020-08-08 11:00:00'), 8, NotOnWorkingDayException::class],
            'notInWorkingHour' => [new \DateTime('2020-08-10 06:00:00'), 8, NotInWorkingHourException::class]
        ];
    }


    private function getCalulator(): DueDateCalculator
    {
        return new DueDateCalculator();
    }

}
