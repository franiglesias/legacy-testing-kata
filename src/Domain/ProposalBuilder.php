<?php
declare (strict_types=1);

namespace Quotebot\Domain;

class ProposalBuilder
{

    private const FROM_DATE = '2000-1-1';
    private const PRICE_CORRECTION = 2;
    private const EVEN_COEFFICIENT = 3.14;
    private const ODD_COEFFICIENT = 3.15;
    private MarketStudyProvider $marketDataRetriever;
    private Clock $clock;

    public function __construct(MarketStudyProvider $marketDataRetriever, Clock $clock)
    {
        $this->marketDataRetriever = $marketDataRetriever;
        $this->clock = $clock;
    }

    private function evenProposalStrategy($proposal): float
    {
        return self::EVEN_COEFFICIENT * $proposal;
    }

    private function isEven($proposal): bool
    {
        return $proposal % 2 === 0;
    }

    public function calculateProposal(Blog $blog, Mode $mode): Proposal
    {
        $startingPrice = $this->correctedAveragePrice($blog);

        $proposalAmount = $this->isEven($startingPrice)
            ? $this->evenProposalStrategy($startingPrice)
            : $this->oddProposalStrategy($mode);

        return new Proposal($proposalAmount);
    }

    private function correctedAveragePrice(Blog $blog)
    {
        return $this->averagePrice($blog) + self::PRICE_CORRECTION;
    }

    private function oddProposalStrategy(Mode $mode)
    {
        return self::ODD_COEFFICIENT
            * $mode->timeFactor()
            * $this->timeDiff(self::FROM_DATE);
    }

    private function timeDiff(string $fromDate): int
    {
        return $this->clock->secondsSince($fromDate);
    }

    private function averagePrice(Blog $blog): float
    {
        return $this->marketDataRetriever->averagePrice($blog);
    }
}
