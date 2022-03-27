<?php
declare(strict_types=1);

namespace Quotebot\Domain;

interface ClockService
{
	public function timestampDiff(string $since): int;
}
