<?php

declare(strict_types=1);

namespace Quotebot;


final class SystemClock implements Clock
{

	public function timeDiff(string $fromDate): int
	{
		$timeDiff = (new \DateTime)->getTimestamp() - (new \DateTime($fromDate))->getTimestamp();

		return (int)ceil($timeDiff / 86400);
	}
}
