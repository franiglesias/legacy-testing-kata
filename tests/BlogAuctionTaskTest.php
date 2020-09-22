<?php

namespace Quotebot;

use Generator;
use PHPUnit\Framework\TestCase;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\CalculateProposal;
use Quotebot\Domain\MarketDataRetriever;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Price;
use Quotebot\Domain\Proposal;
use Quotebot\Domain\ProposalPublisher;
use Quotebot\Domain\TimeService;

class BlogAuctionTaskTest extends TestCase
{

    private $marketDataRetriever;
    private $proposalPublisher;
    private $timeService;
    private $blogAuctionTask;

    protected function setUp(): void
    {
        $this->marketDataRetriever = $this->createMock(MarketDataRetriever::class);
        $this->proposalPublisher = $this->createMock(ProposalPublisher::class);
        $this->timeService = $this->createMock(TimeService::class);

        $this->blogAuctionTask = new BlogAuctionTask(
            $this->marketDataRetriever,
            $this->proposalPublisher,
            new CalculateProposal($this->timeService)
        );
    }

    /** @dataProvider casesProvider */
    public function testShouldSendAProposal($averagePrice, Mode $mode, Proposal $proposal): void
    {
        $this->givenTimeIntervalIs(1);
        $this->givenAnAveragePrice($averagePrice);
        $this->thenAProposalIsSentOf($proposal);
        $this->whenIsPricedWithMode($mode);
    }

    public function casesProvider(): Generator
    {
        yield 'Odd path basic calculation' =>  [new Price(0), new Mode('SLOW'), new Proposal(6.28)];
        yield 'Even path basic calculation' => [new Price(1), new Mode('SLOW'), new Proposal(6.30)];
    }

    protected function givenAnAveragePrice($averagePrice): void
    {
        $this->marketDataRetriever
            ->method('averagePrice')
            ->willReturn($averagePrice);
    }

    protected function thenAProposalIsSentOf($proposal): void
    {
        $this->proposalPublisher
            ->expects(self::once())
            ->method('publish')
            ->with($proposal);
    }

    protected function whenIsPricedWithMode($mode): void
    {
        $this->blogAuctionTask->priceAndPublish('blog', $mode);
    }

    private function givenTimeIntervalIs($interval): void
    {
        $this->timeService->method('timeInterval')->willReturn($interval);
    }
}
