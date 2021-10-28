<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\Clock;

use DateTime;
use Quotebot\Domain\Clock;

class SystemClock implements Clock
{

    public function secondsSince(string $fromDate): int
    {
        return (new DateTime())->getTimestamp() - (new DateTime($fromDate))->getTimestamp();
    }
}
