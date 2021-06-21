<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\Publisher;
use Quotebot\Infrastructure\VendorQuotePublisher;

class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;
	private ?Publisher $publisher;

	public function __construct(
    	?MarketStudyVendor $marketStudyVendor = null,
		?Publisher $publisher = null
	)
    {
        $this->marketDataRetriever = $marketStudyVendor ?? new MarketStudyVendor();
		$this->publisher = $publisher ?? new VendorQuotePublisher();
	}

    public function priceAndPublish(string $blog, string $mode)
    {
		$avgPrice = $this->averagePrice($blog);

		// FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
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
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();

		$this->publishProposal($proposal);
	}

	protected function publishProposal($proposal): void
	{
		$this->publisher->publishProposal($proposal);
	}

	protected function averagePrice(string $blog): float
	{
		return $this->marketDataRetriever->averagePrice($blog);
	}
}
