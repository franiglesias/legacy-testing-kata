<?php

namespace Quotebot;

use Quotebot\Domain\MarketDataRetriever;
use Quotebot\Domain\ProposalPublisher;
use Quotebot\Domain\TimeService;

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
        $timeInterval = $this->timeService->timeInterval();
        return 3.15
            * $timeFactor
            * $timeInterval;
    }
}
