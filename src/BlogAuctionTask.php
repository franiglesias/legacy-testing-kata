<?php

namespace Quotebot;

use Quotebot\Domain\Blog;
use Quotebot\Domain\MarketStudyProvider;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Publisher;
use Quotebot\Infrastructure\Publisher\VendorPublisher;

class BlogAuctionTask
{
    private const PRICE_CORRECTION = 2;
    private const EVEN_COEFFICIENT = 3.14;
    private const ODD_COEFFICIENT = 3.15;
    private const FROM_DATE = '2000-1-1';

    private MarketStudyProvider $marketDataRetriever;
    private Publisher $publisher;

    public function __construct(
        MarketStudyProvider $marketStudyVendor,
        ?Publisher $publisher = null
    ) {
        $this->marketDataRetriever = $marketStudyVendor;
        $this->publisher = $publisher ?? new VendorPublisher();
    }

    protected function averagePrice(Blog $blog): float
    {
        return $this->marketDataRetriever->averagePrice($blog);
    }

    public function priceAndPublish(string $blogName, string $modeName): void
    {
        $blog = new Blog($blogName);
        $mode = new Mode($modeName);

        $proposal = $this->calculateProposal($blog, $mode);

        $this->publishProposal($proposal);
    }

    protected function timeDiff(string $fromDate): int
    {
        return (new \DateTime())->getTimestamp() - (new \DateTime($fromDate))->getTimestamp();
    }

    protected function publishProposal($proposal): void
    {
        $this->publisher->publish($proposal);
    }

    private function evenProposalStrategy($proposal): float
    {
        return self::EVEN_COEFFICIENT * $proposal;
    }

    private function oddProposalStrategy(Mode $mode)
    {
        return self::ODD_COEFFICIENT
            * $mode->timeFactor()
            * $this->timeDiff(self::FROM_DATE);
    }

    private function isEven($proposal): bool
    {
        return $proposal % 2 === 0;
    }

    private function correctedAveragePrice(Blog $blog)
    {
        return $this->averagePrice($blog) + self::PRICE_CORRECTION;
    }

    private function calculateProposal(Blog $blog, Mode $mode)
    {
        $proposal = $this->correctedAveragePrice($blog);

        return $this->isEven($proposal)
            ? $this->evenProposalStrategy($proposal)
            : $this->oddProposalStrategy($mode);
    }
}
