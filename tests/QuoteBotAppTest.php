<?php

namespace Tests\Quotebot;

use Quotebot\Application;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use PHPUnit\Framework\TestCase;

class QuoteBotAppTest extends TestCase
{
    public function testShouldRun(): void
    {
        $blogAuctionTask = new class() extends BlogAuctionTask {
            protected function publishProposal(int $proposal): void
            {
            }
        };

        $automaticQuoteBot = new class($blogAuctionTask) extends AutomaticQuoteBot {
            protected function getBlogs(string $mode): array
            {
                return ['Blog1', 'Blog2'];
            }
        };

        Application::inject($automaticQuoteBot);
        Application::main();

        self::assertTrue(true);
    }

}
