<?php

namespace Quotebot;

use Quotebot\Domain\Blog;
use Quotebot\Domain\Clock;
use Quotebot\Domain\MarketStudyProvider;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Proposal;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Domain\Publisher;
use Quotebot\Infrastructure\Clock\SystemClock;
use Quotebot\Infrastructure\Publisher\VendorPublisher;

class BlogAuctionTask
{

    protected Publisher $publisher;
    private ProposalBuilder $proposalBuilder;

    public function __construct(
        Publisher $publisher,
        ProposalBuilder $proposalBuilder
    ) {
        $this->publisher = $publisher;
        $this->proposalBuilder = $proposalBuilder;
    }

    public function priceAndPublish(Blog $blog, Mode $mode): void
    {
        $proposal = $this->calculateProposal($blog, $mode);

        $this->publishProposal($proposal);
    }

    private function publishProposal(Proposal $proposal): void
    {
        $this->publisher->publish($proposal);
    }

    private function calculateProposal(Blog $blog, Mode $mode): Proposal
    {
        return $this->proposalBuilder->calculateProposal($blog, $mode);
    }
}
