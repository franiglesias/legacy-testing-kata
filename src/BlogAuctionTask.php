<?php

namespace Quotebot;

class BlogAuctionTask
{
    /** @var MarketStudyInterface */
    private $marketDataRetriever;
    private $publisher;
    private $timeService;

    public function __construct(
        MarketStudyInterface $marketStudyVendor,
        PublisherInterface $publisher,
        TimeServiceInterface $timeService

    ) {
        $this->marketDataRetriever = $marketStudyVendor;
        $this->publisher = $publisher;
        $this->timeService = $timeService;
    }

    public function priceAndPublish(string $blog, string $mode)
    {
        $avgPrice = $this->marketDataRetriever->averagePrice($blog);

        // FIXME should actually be +2 not +1

        $proposal = $avgPrice + 1;
        $timeFactor = $this->calculateTimeFactor($mode);

        $today = $this->timeService->getTodayTimestamp();
        $origin = $this->timeService->getTimestamp('2000-1-1');

        $proposal = $proposal % 2 === 0 ? 3.14 * $proposal : 3.15
            * $timeFactor
            * $today - $origin;

        $this->publish($proposal);
    }

    /**
     * @param $proposal
     */
    protected function publish($proposal): void
    {
        $this->publisher->publish($proposal);
    }

    /**
     * @param string $mode
     * @return int
     */
    private function calculateTimeFactor(string $mode): int
    {
        $timeFactor = 1;

        switch ($mode) {
            case 'SLOW':
                $timeFactor = 2;
                break;

            case 'MEDIUM':
                $timeFactor = 4;
                break;

            case 'ULTRAFAST':
            case 'FAST':
                $timeFactor = 8;
                break;
        }

        return $timeFactor;
    }

}
