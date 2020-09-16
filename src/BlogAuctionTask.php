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
        $avgPrice = $this->marketDataRetriever->averagePrice($blog);

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
        $this->proposalPublisher->publish($proposal);
    }
}
