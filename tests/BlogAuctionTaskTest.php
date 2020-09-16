<?php

namespace Quotebot;

use Generator;
use PHPUnit\Framework\TestCase;
use Quotebot\Domain\ProposalPublisher;

class BlogAuctionTaskTest extends TestCase
{

    private $marketStudyVendor;
    private $proposalPublisher;

    protected function setUp()
    {
        $this->marketStudyVendor = $this->createMock(\MarketStudyVendor::class);
        $this->proposalPublisher = $this->createMock(ProposalPublisher::class);
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
        $blogAuctionTask = new BlogAuctionTask($this->marketStudyVendor, $this->proposalPublisher);
        $blogAuctionTask->priceAndPublish('blog', $mode);
    }
}
