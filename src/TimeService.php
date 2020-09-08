<?php
declare (strict_types=1);

namespace Quotebot;

use DateTime;

class TimeService implements TimeServiceInterface
{

    public function getTimestamp($date = null)
    {
        return (new DateTime($date))->getTimestamp();

    }

    public function getTodayTimestamp()
    {
        return $this->getTimestamp();
    }
}
