<?php
declare(strict_types=1);

namespace AppBundle\Validator;


use AppBundle\Exception\AbstractNotInWorkingRangeInterface;

interface DateValidatorInterface
{
    /**
     * @param \DateTimeInterface $date
     *
     * @throws AbstractNotInWorkingRangeInterface
     */
    public function validate(\DateTimeInterface $date): void;
}