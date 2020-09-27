<?php

namespace Quotebot;

use Generator;
use PHPUnit\Framework\TestCase;
use Quotebot\Application\BlogAuctionTask;
use Quotebot\Domain\AdSpace\Blog;
use Quotebot\Domain\MarketData\MarketDataRetriever;
use Quotebot\Domain\MarketData\Price;
use Quotebot\Domain\Proposal\CalculateProposal;
use Quotebot\Domain\Proposal\Mode;
use Quotebot\Domain\Proposal\Proposal;
use Quotebot\Domain\Proposal\TimeService;

class BlogAuctionTaskTest extends TestCase
{

    private $marketDataRetriever;
    private $timeService;
    private $blogAuctionTask;

    protected function setUp(): void
    {
        $this->marketDataRetriever = $this->createMock(MarketDataRetriever::class);
        $this->timeService = $this->createMock(TimeService::class);

        $this->blogAuctionTask = new BlogAuctionTask(
            $this->marketDataRetriever,
            new CalculateProposal($this->timeService)
        );
    }

    /** @dataProvider casesProvider */
    public function testShouldSendAProposal($averagePrice, Mode $mode, Proposal $proposal): void
    {
        $this->givenTimeIntervalIs(1);
        $this->givenAnAveragePrice($averagePrice);
        $this->thenAProposalIsCalculatedOf($mode, $proposal);
    }

    public function casesProvider(): Generator
    {
        yield 'Odd path basic calculation' => [new Price(0), new Mode('SLOW'), new Proposal(6.28)];
        yield 'Even path basic calculation' => [new Price(1), new Mode('SLOW'), new Proposal(6.30)];
    }

    protected function givenAnAveragePrice($averagePrice): void
    {
        $this->marketDataRetriever
            ->method('averagePrice')
            ->willReturn($averagePrice);
    }

    protected function thenAProposalIsCalculatedOf($mode, $proposal): void
    {
        $generated = $this->blogAuctionTask->generateProposal(new Blog('blog'), $mode);

        self::assertEquals($proposal, $generated);
    }

    private function givenTimeIntervalIs($interval): void
    {
        $this->timeService->method('timeInterval')->willReturn($interval);
    }
}
