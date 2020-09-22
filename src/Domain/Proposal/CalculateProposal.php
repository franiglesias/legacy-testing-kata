<?php


namespace Quotebot\Domain\Proposal;


use Quotebot\Domain\MarketData\Price;

class CalculateProposal
{
    /**
     * @var TimeService
     */
    private $timeService;

    public function __construct(TimeService $timeService)
    {
        $this->timeService = $timeService;
    }

    public function fromPrice(Price $averagePrice, Mode $mode): Proposal
    {
        $proposal = $averagePrice->getPrice() + 2;

        $proposal = $proposal % 2 === 0
            ? $this->calculateEvenProposal($proposal)
            : $this->calculateOddProposal($mode);

        return new Proposal($proposal);
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