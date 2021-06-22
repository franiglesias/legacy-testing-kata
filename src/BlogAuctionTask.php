<?php

namespace Quotebot;

use Quotebot\Domain\CalculateProposal;
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Publisher;

class BlogAuctionTask
{
	private MarketDataProvider $marketDataRetriever;
	private Publisher $publisher;
	private CalculateProposal $calculateProposal;

	public function __construct(
		MarketDataProvider $marketStudyVendor,
		Publisher $publisher,
		CalculateProposal $calculateProposal
	) {
		$this->marketDataRetriever = $marketStudyVendor;
		$this->publisher           = $publisher;
		$this->calculateProposal   = $calculateProposal;
	}

	public function priceAndPublish(string $blog, Mode $mode): void
	{
		$avgPrice = $this->averagePrice($blog);

		$proposal = $this->proposal($mode, $avgPrice);

		$this->publishProposal($proposal);
	}

	private function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}

	private function proposal(Mode $mode, float $avgPrice)
	{
		return $this->calculateProposal->proposal($mode, $avgPrice);
	}

	private function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}
}
