<?php
declare (strict_types=1);

namespace Quotebot;

use PHPUnit\Framework\TestCase;

class BlogAuctionTaskTest extends TestCase
{
    public function testShouldDoSomething(): void
    {
        $marketStudyVendor = new MarketStudy();
        $publisher = $this->createMock(PublisherInterface::class);
        $publisher->expects(self::once())
            ->method('publish')
            ->with(2520);

        $timeService = $this->createMock(TimeServiceInterface::class);
        $timeService->method('getTodayTimestamp')->willReturn(100);
        $timeService->method('getTimestamp')->willReturn(0);
        $blogAuctionTask = new BlogAuctionTask(
            $marketStudyVendor,
            $publisher,
            $timeService
        );

        $blogAuctionTask->priceAndPublish('miblog', 'FAST');

        $this->assertTrue(true);
    }

}
