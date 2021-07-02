<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\Blog;

class BlogAuctionTask
{
    private const PRICE_CORRECTION = 2;
    private const EVEN_COEFFICIENT = 3.14;
    private const ODD_COEFFICIENT = 3.15;
    private const FROM_DATE = '2000-1-1';

    /** @var MarketStudyVendor */
    private $marketDataRetriever;

    public function __construct()
    {
        $this->marketDataRetriever = new MarketStudyVendor();
    }

    public function priceAndPublish(string $blogName, string $modeName): void
    {
        $blog = new Blog($blogName);

        $proposal = $this->calculateProposal($blog, $modeName);

        $this->publishProposal($proposal);
    }

    protected function averagePrice(Blog $blog): float
    {
        return $this->marketDataRetriever->averagePrice($blog->name());
    }

    protected function timeDiff(string $fromDate): int
    {
        return (new \DateTime())->getTimestamp() - (new \DateTime($fromDate))->getTimestamp();
    }

    protected function publishProposal($proposal): void
    {
        \QuotePublisher::publish($proposal);
    }

    private function evenProposalStrategy($proposal): float
    {
        return self::EVEN_COEFFICIENT * $proposal;
    }

    private function oddProposalStrategy(string $mode)
    {
        $timeFactor = $this->timeFactor($mode);
        $timeDiff = $this->timeDiff(self::FROM_DATE);

        return self::ODD_COEFFICIENT * $timeFactor * $timeDiff;
    }

    private function timeFactor(string $mode): int
    {
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

        return $timeFactor;
    }

    private function isEven($proposal): bool
    {
        return $proposal % 2 === 0;
    }

    private function correctedAveragePrice(Blog $blog)
    {
        return $this->averagePrice($blog) + self::PRICE_CORRECTION;
    }

    private function calculateProposal(Blog $blog, string $mode)
    {
        $proposal = $this->correctedAveragePrice($blog);

        $proposal = $this->isEven($proposal)
            ? $this->evenProposalStrategy($proposal)
            : $this->oddProposalStrategy($mode);

        return $proposal;
    }
}
