<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\Mode;

class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;

	public function __construct()
    {
        $this->marketDataRetriever = new MarketStudyVendor();
    }

	public function priceAndPublish(string $blog, Mode $mode)
    {

		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

		$proposal = $avgPrice + 1;

		$timeFactor = $mode->timeFactor();

		$proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}

	protected function publishProposal($proposal): void
	{
		\QuotePublisher::publish($proposal);
	}
}
