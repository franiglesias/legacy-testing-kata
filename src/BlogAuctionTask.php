<?php

namespace Quotebot;

use Quotebot\Domain\ClockService;
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Publisher;

class BlogAuctionTask
{
	private MarketDataProvider $marketDataRetriever;
	private Publisher $publisher;
	private ClockService $clockService;

	public function __construct(
		MarketDataProvider $marketStudyVendor,
		Publisher $publisher,
		ClockService $clockService
	) {
		$this->marketDataRetriever = $marketStudyVendor;
		$this->publisher           = $publisher;
		$this->clockService        = $clockService;
	}

	public function priceAndPublish(string $blog, Mode $mode): void
	{
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

		$proposal = $avgPrice + 1;

		$timeFactor = $mode->timeFactor();

		$proposal = $proposal % 2 === 0
			? 3.14 * $proposal
			: 3.15 * $timeFactor * $this->timestampDiff('2000-1-1');

		$this->publishProposal($proposal);
	}

	private function timestampDiff(string $since): int
	{
		return $this->clockService->timestampDiff($since);
	}

	private function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	private function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
