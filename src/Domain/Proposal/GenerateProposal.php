<?php

namespace Quotebot\Domain\Proposal;

use Quotebot\Domain\AdSpace\AdSpace;
use Quotebot\Domain\MarketData\MarketDataRetriever;

class GenerateProposal
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

    public function forAdSpace(AdSpace $blog, Mode $mode): \Quotebot\Domain\Proposal\Proposal
    {
        $averagePrice = $this->marketDataRetriever->averagePrice($blog);

        return $this->calculateProposal->fromPrice(
            $averagePrice,
            $mode
        );
    }
}
