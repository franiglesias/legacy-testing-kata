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
    /** @var ProposalPublisher|null */
    private $proposalPublisher;
    /** @var CalculateProposal */
    private $calculateProposal;

    public function __construct(
        MarketDataRetriever $marketDataRetriever,
        ProposalPublisher $proposalPublisher,
        CalculateProposal $calculateProposal
    )
    {
        $this->marketDataRetriever = $marketDataRetriever;
        $this->proposalPublisher = $proposalPublisher;
        $this->calculateProposal = $calculateProposal;
    }

    public function priceAndPublish(AdSpace $blog, Mode $mode): void
    {
        $proposal = $this->generateProposal($blog, $mode);

        $this->publishProposal($proposal);
    }

    public function publishProposal($proposal): void
    {
        $this->proposalPublisher->publish($proposal);
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
