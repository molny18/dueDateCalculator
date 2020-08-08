<?php

declare(strict_types=1);

namespace AppBundle\Exception;

class NotInWorkingHourException extends \Exception implements AbstractNotInWorkingRangeInterface
{
}
