<?php

namespace Quotebot;

use Generator;
use PHPUnit\Framework\TestCase;
use Quotebot\Domain\ProposalPublisher;
use Quotebot\Domain\TimeService;

class BlogAuctionTaskTest extends TestCase
{

    private $marketStudyVendor;
    private $proposalPublisher;
    private $timeService;
    private $blogAuctionTask;

    protected function setUp(): void
    {
        $this->marketStudyVendor = $this->createMock(\MarketStudyVendor::class);
        $this->proposalPublisher = $this->createMock(ProposalPublisher::class);
        $this->timeService = $this->createMock(TimeService::class);

        $this->blogAuctionTask = new BlogAuctionTask(
            $this->marketStudyVendor,
            $this->proposalPublisher,
            $this->timeService
        );
    }

    /** @dataProvider casesProvider */
    public function testShouldSendAProposal($averagePrice, $mode, $proposal): void
    {
        $this->givenAnAveragePrice($averagePrice);
        $this->thenAProposalIsSentOf($proposal);
        $this->whenIsPricedWIthMode($mode);
    }

    public function casesProvider(): Generator
    {
        yield 'Odd path basic calculation' =>  [0, 'SLOW', 6.28];
    }

    protected function givenAnAveragePrice($averagePrice): void
    {
        $this->marketStudyVendor
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

    protected function whenIsPricedWIthMode($mode): void
    {
        $this->blogAuctionTask->priceAndPublish('blog', $mode);
    }
}
