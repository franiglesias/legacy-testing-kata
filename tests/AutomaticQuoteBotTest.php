<?php
declare (strict_types=1);

namespace Quotebot\Tests;

use PHPUnit\Framework\TestCase;
use Quotebot\AutomaticQuoteBot;
use Quotebot\BlogAuctionTask;
use Quotebot\Domain\AdSpaceRepository;
use Quotebot\Domain\Blog;
use Quotebot\Domain\Mode;

class AutomaticQuoteBotTest extends TestCase
{
    private AutomaticQuoteBot $bot;
    private BlogAuctionTask $blogAuctionTaskSpy;
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
            ->willReturn([new Blog('Blog1'), new Blog('Blog2')]);

        $this->bot->sendAllQuotes('FAST');

        self::assertTrue($this->blogAuctionTaskSpy->hasBeenCalled());
        self::assertTrue($spy->hasBeenInvoked());
    }

    protected function buildBlogAuctionTaskSpy(): BlogAuctionTask
    {
        return new class() extends BlogAuctionTask {
            private int $calls = 0;

            public function __construct()
            {
            }

            public function priceAndPublish(Blog $blog, Mode $mode): void
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
