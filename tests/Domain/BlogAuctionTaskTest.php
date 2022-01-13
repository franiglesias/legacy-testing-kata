<?php
declare (strict_types=1);

namespace Quotebot\Tests\Domain;

use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount as PublisherSpy;
use PHPUnit\Framework\TestCase;
use Quotebot\Domain\Blog;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\Clock;
use Quotebot\Domain\MarketStudyProvider;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Proposal;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Domain\Publisher;

class BlogAuctionTaskTest extends TestCase
{
    private Publisher $publisher;
    private Clock $clock;
    private MarketStudyProvider $marketStudyProvider;
    private BlogAuctionTask $blogAuctionTask;

    protected function setUp(): void
    {
        $this->publisher = $this->createMock(Publisher::class);
        $this->buildClockAlwaysReturning(1);
        $this->marketStudyProvider = $this->createMock(MarketStudyProvider::class);
        $this->blogAuctionTask = $this->buildBlogAuctionTask();
    }

    /**
     * @test
     * @dataProvider examplesProvider
     */
    public function shouldGenerateAProposal(Mode $mode, float $averagePrice, Proposal $proposal): void
    {
        $blog = new Blog('Blog Example');

        $this->givenMarketStudyGivesPriceForBlog($blog, $averagePrice);
        $publisherSpy = $this->expectingProposalToBePublished($proposal);

        $this->blogAuctionTask->priceAndPublish($blog, $mode);

        self::assertTrue($publisherSpy->hasBeenInvoked());
    }

    public function examplesProvider(): array
    {
        return [
            'odd slow' => [new Mode('SLOW'), 0.0, new Proposal(6.28)],
            'even slow' => [new Mode('SLOW'), 1.0, new Proposal(6.3)],
            'odd medium' => [new Mode('MEDIUM'), 0.0, new Proposal(6.28)],
            'even medium' => [new Mode('MEDIUM'), 1.0, new Proposal(12.6)],
        ];
    }

    private function buildBlogAuctionTask(): BlogAuctionTask
    {
        $proposalBuilder = new ProposalBuilder($this->marketStudyProvider, $this->clock);

        return new BlogAuctionTask($this->publisher, $proposalBuilder);
    }

    protected function buildClockAlwaysReturning(int $seconds): void
    {
        $this->clock = $this->createMock(Clock::class);
        $this->clock->method('secondsSince')->willReturn($seconds);
    }

    protected function givenMarketStudyGivesPriceForBlog(Blog $blog, float $averagePrice): void
    {
        $this->marketStudyProvider
            ->method('averagePrice')
            ->with($blog)
            ->willReturn($averagePrice);
    }

    protected function expectingProposalToBePublished(Proposal $proposal): PublisherSpy
    {
        $this->publisher
            ->expects($publisherSpy = self::any())
            ->method('publish')
            ->with($proposal);

        return $publisherSpy;
    }
}
