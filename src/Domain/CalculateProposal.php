<?php
declare(strict_types=1);

namespace Quotebot\Domain;


class CalculateProposal
{
	private ClockService $clockService;

	public function __construct(ClockService $clockService)
	{
		$this->clockService = $clockService;
	}

	public function proposal(Mode $mode, float $avgPrice)
	{
		$timeFactor = $mode->timeFactor();

		$proposal   = $avgPrice + 2;

		return $proposal % 2 === 0
			? 3.14 * $proposal
			: 3.15 * $timeFactor * $this->timestampDiff('2000-1-1');
	}

	public function timestampDiff(string $since): int
	{
		return $this->clockService->timestampDiff($since);
	}
}
