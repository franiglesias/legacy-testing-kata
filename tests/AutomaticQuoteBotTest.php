<?php
declare (strict_types=1);

namespace Quotebot\Tests;

use PHPUnit\Framework\TestCase;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;

class AutomaticQuoteBotTest extends TestCase
{
    /** @test */
    public function shouldUseInjectedDependency(): void
    {
        $blogAuctionTask = $this->buildBlogAuctionTaskSpy();

        $bot = new AutomaticQuoteBot($blogAuctionTask);
        $bot->sendAllQuotes('FAST');

        self::assertTrue($blogAuctionTask->hasBeenCalled());
    }

    protected function buildBlogAuctionTaskSpy()
    {
        return new class() extends BlogAuctionTask {
            private int $calls = 0;

            public function __construct()
            {
            }

            public function priceAndPublish(string $blogName, string $modeName): void
            {
                $this->calls++;
            }

            public function hasBeenCalled(): bool
            {
                return $this->calls > 0;
            }
        };
    }

}
