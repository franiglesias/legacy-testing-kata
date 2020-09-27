<?php

namespace Quotebot\Application;

use Quotebot\Domain\AdSpace\AdSpace;
use Quotebot\Domain\MarketData\MarketDataRetriever;
use Quotebot\Domain\Proposal\CalculateProposal;
use Quotebot\Domain\Proposal\Mode;
use Quotebot\Domain\ProposalPublisher;

class BlogAuctionTask
{
    /** @var MarketDataRetriever */
    protected $marketDataRetriever;
    /** @var CalculateProposal */
    private $calculateProposal;

    public function __construct(
        MarketDataRetriever $marketDataRetriever, CalculateProposal $calculateProposal
    )
    {
        $this->marketDataRetriever = $marketDataRetriever;
        $this->calculateProposal = $calculateProposal;
    }

    public function generateProposal(AdSpace $blog, Mode $mode): \Quotebot\Domain\Proposal\Proposal
    {
        $averagePrice = $this->marketDataRetriever->averagePrice($blog);

        return $this->calculateProposal->fromPrice(
            $averagePrice,
            $mode
        );
    }
}
