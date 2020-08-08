<?php

declare(strict_types=1);

namespace AppBundle\Exception;

class NotOnWorkingDayException extends \Exception implements AbstractNotInWorkingRangeInterface
{
}
