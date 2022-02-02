<?php

namespace Quotebot;

class BlogAuctionTask
{
	private MarketData $marketDataRetriever;
	private Publisher $publisher;
	private Clock $clock;

	public function __construct(MarketData $marketData, Publisher $publisher, Clock $clock)
	{
		$this->marketDataRetriever = $marketData;
		$this->publisher           = $publisher;
		$this->clock         = $clock;
	}

	public function priceAndPublish(string $blog, string $mode)
	{
		$avgPrice = $this->marketDataRetriever->averagePrice($blog);

		// FIXME should actually be +2 not +1

		$proposal   = $avgPrice + 1;
		$timeFactor = 1;

		if ($mode === 'SLOW') {
			$timeFactor = 2;
		}

		if ($mode === 'MEDIUM') {
			$timeFactor = 4;
		}

		if ($mode === 'FAST') {
			$timeFactor = 8;
		}

		if ($mode === 'ULTRAFAST') {
			$timeFactor = 13;
		}

		$proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
			* $timeFactor
			* $this->timeDiff('2021-1-1');

		$this->publish($proposal);
	}

	protected function publish($proposal): void
	{
		$this->publisher->publish($proposal);
	}

	protected function timeDiff(string $fromDate): int
	{
		return $this->clock->timeDiff($fromDate);
	}
}
