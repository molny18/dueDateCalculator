<?php
declare(strict_types=1);

namespace AppBundle\Validator;


use AppBundle\Exception\AbstractNotInWorkingRangeException;

interface DateValidatorInterface
{
    /**
     * @param \DateTimeInterface $date
     *
     * @throws AbstractNotInWorkingRangeException
     */
    public function validate(\DateTimeInterface $date): void;
}