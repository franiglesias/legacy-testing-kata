<?php
declare(strict_types=1);

namespace Quotebot\Infrastructure;


use Quotebot\Domain\ClockService;

class SystemClockService implements ClockService
{

	public function timestampDiff(string $since): int
	{
		return (new \DateTime)->getTimestamp()
			- (new \DateTime($since))->getTimestamp();
	}
}
