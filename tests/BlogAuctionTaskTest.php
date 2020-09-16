<?php

namespace Quotebot;

use PHPUnit\Framework\TestCase;
use Quotebot\Domain\ProposalPublisher;

class BlogAuctionTaskTest extends TestCase
{

    public function testShouldSendAProposal(): void
    {
        $marketStudyVendor = $this->createMock(\MarketStudyVendor::class);
        $marketStudyVendor
            ->method('averagePrice')
            ->willReturn(0);

        $proposalPublisher = $this->createMock(ProposalPublisher::class);
        $proposalPublisher
            ->expects(self::once())
            ->method('publish')
            ->with(6.28);

        $blogAuctionTask = new BlogAuctionTask($marketStudyVendor, $proposalPublisher);
        $blogAuctionTask->priceAndPublish('blog', 'SLOW');

    }
}
