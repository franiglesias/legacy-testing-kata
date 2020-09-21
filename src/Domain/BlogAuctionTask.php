<?php

namespace Quotebot\Domain;

class BlogAuctionTask
{
    /** @var MarketDataRetriever */
    protected $marketDataRetriever;
    /** @var ProposalPublisher|null */
    private $proposalPublisher;
    /** @var TimeService */
    private $timeService;

    public function __construct(
        MarketDataRetriever $marketDataRetriever,
        ProposalPublisher $proposalPublisher,
        TimeService $timeService
    )
    {
        $this->marketDataRetriever = $marketDataRetriever;
        $this->proposalPublisher = $proposalPublisher;
        $this->timeService = $timeService;
    }

    public function priceAndPublish(string $blog, Mode $mode)
    {
        $averagePrice = $this->marketDataRetriever->averagePrice($blog);

        $proposal = $averagePrice + 2;

        $proposal = $proposal % 2 === 0
            ? $this->calculateEvenProposal($proposal)
            : $this->calculateOddProposal($mode);

        $this->publishProposal($proposal);
    }

    protected function publishProposal($proposal): void
    {
        $this->proposalPublisher->publish($proposal);
    }

    private function calculateEvenProposal(int $proposal): float
    {
        return 3.14 * $proposal;
    }

    private function calculateOddProposal(Mode $mode)
    {
        $timeInterval = $this->timeService->timeInterval();
        return 3.15
            * $mode->timeFactor()
            * $timeInterval;
    }
}
