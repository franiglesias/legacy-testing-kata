<?php
declare (strict_types=1);

namespace Quotebot\Tests;

use PHPUnit\Framework\TestCase;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Domain\AdSpaceRepository;

class AutomaticQuoteBotTest extends TestCase
{
    private AutomaticQuoteBot $bot;
    private $blogAuctionTaskSpy;
    private $adSpaceRepository;

    protected function setUp(): void
    {
        $this->blogAuctionTaskSpy = $this->buildBlogAuctionTaskSpy();

        $this->adSpaceRepository = $this->createMock(AdSpaceRepository::class);

        $this->bot = new AutomaticQuoteBot(
            $this->blogAuctionTaskSpy,
            $this->adSpaceRepository
        );
    }

    /** @test */
    public function shouldUseInjectedDependency(): void
    {
        $this->adSpaceRepository
            ->expects($spy = self::any())
            ->method('findAll')
            ->willReturn(['Blog1', 'Blog2']);

        $this->bot->sendAllQuotes('FAST');

        self::assertTrue($this->blogAuctionTaskSpy->hasBeenCalled());
        self::assertTrue($spy->hasBeenInvoked());
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
