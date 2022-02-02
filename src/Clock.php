<?php

declare(strict_types=1);

namespace Quotebot;

interface Clock
{
	public function timeDiff(string $fromDate): int;
}
