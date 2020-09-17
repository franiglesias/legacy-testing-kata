<?php


namespace Quotebot\Infrastructure;


use Quotebot\Domain\TimeService;

class SystemTimeService implements TimeService
{

    public function timeInterval(): int
    {
        return (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();
    }
}