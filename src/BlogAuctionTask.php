<?php

namespace Quotebot;

use MarketStudyVendor;
use Quotebot\Domain\ProposalPublisher;
use Quotebot\Infrastructure\QuoteProposalPublisher;

class BlogAuctionTask
{
    /** @var MarketStudyVendor */
    protected $marketDataRetriever;
    /**
     * @var ProposalPublisher|null
     */
    private $proposalPublisher;

    public function __construct(
        $marketDataRetriever = null,
        ?ProposalPublisher $proposalPublisher = null
    )
    {
        $this->marketDataRetriever = $marketDataRetriever ?? new MarketStudyVendor();
        $this->proposalPublisher = $proposalPublisher ?? new QuoteProposalPublisher();
    }

    public function priceAndPublish(string $blog, string $mode)
    {
        $averagePrice = $this->marketDataRetriever->averagePrice($blog);

        $proposal = $averagePrice + 2;

        $timeFactor = $this->timeFactor($mode);

        $proposal = $proposal % 2 === 0
            ? $this->calculateEvenProposal($proposal)
            : $this->calculateOddProposal($timeFactor);

        $this->publishProposal($proposal);
    }

    protected function publishProposal($proposal): void
    {
        $this->proposalPublisher->publish($proposal);
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

    private function calculateEvenProposal(int $proposal): float
    {
        return 3.14 * $proposal;
    }

    private function calculateOddProposal(int $timeFactor)
    {
        return 3.15
            * $timeFactor
            * (new \DateTime())->getTimestamp() - (new \DateTime('2000-1-1'))->getTimestamp();
    }
}
