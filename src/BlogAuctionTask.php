<?php

namespace Quotebot;

use MarketStudyVendor;

class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    private $marketDataRetriever;

    public function __construct()
    {
        $this->marketDataRetriever = new MarketStudyVendor();
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

        $timeDiff = $this->timeDiff('2000-1-1');
        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * $timeDiff;

        $this->publishProposal($proposal);
    }

    protected function averagePrice(string $blog): float
    {
        return $this->marketDataRetriever->averagePrice($blog);
    }

    protected function timeDiff(string $fromDate): int
    {
        return (new \DateTime())->getTimestamp() - (new \DateTime($fromDate))->getTimestamp();
    }

    protected function publishProposal($proposal): void
    {
        \QuotePublisher::publish($proposal);
    }
}
