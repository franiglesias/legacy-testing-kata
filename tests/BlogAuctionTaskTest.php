<?php

namespace Tests\Quotebot;

use Quotebot\BlogAuctionTask;
use PHPUnit\Framework\TestCase;

class BlogAuctionTaskTest extends TestCase
{
    public function testShouldRun(): void
    {

        $blogAuctionTask = new class() extends BlogAuctionTask {
            protected function publishProposal(int $proposal): void
            {
            }
        };

        $blogAuctionTask->priceAndPublish('blog', 'SLOW');

        self::assertTrue(true);
    }

}
