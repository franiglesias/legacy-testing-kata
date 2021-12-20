<?php
declare (strict_types=1);

namespace Quotebot\Tests\Application;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Rule\AnyInvokedCount;
use PHPUnit\Framework\TestCase;
use Quotebot\Application\AutomaticQuoteBot;
use Quotebot\Domain\AdSpaceRepository;
use Quotebot\Domain\Blog;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Proposal;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Domain\Publisher;

class AutomaticQuoteBotTest extends TestCase
{
    private AutomaticQuoteBot $bot;
    private BlogAuctionTask $blogAuctionTask;
    private AdSpaceRepository $adSpaceRepository;
    /** @var Publisher|MockObject */
    private Publisher $publisher;
    /** @var ProposalBuilder|MockObject */
    private ProposalBuilder $proposalBuilder;

    protected function setUp(): void
    {
        $this->blogAuctionTask = $this->buildBlogAuctionTask();

        $this->adSpaceRepository = $this->createMock(AdSpaceRepository::class);

        $this->bot = new AutomaticQuoteBot(
            $this->blogAuctionTask,
            $this->adSpaceRepository
        );
    }

    /** @test */
    public function shouldPublishAProposalForEachBlog(): void
    {
        $blogs = [new Blog('Blog1'), new Blog('Blog2')];

        $publisherSpy = $this->givenWeHaveThisCollectionOf($blogs);
        $this->whenWeSendAllQuotes();
        $this->thenWePublishProposalsForEachBlog($blogs, $publisherSpy);
    }

    private function buildBlogAuctionTask(): BlogAuctionTask
    {
        $this->publisher = $this->createMock(Publisher::class);
        $this->proposalBuilder = $this->createMock(ProposalBuilder::class);

        return new BlogAuctionTask(
            $this->publisher,
            $this->proposalBuilder
        );
    }

    private function givenWeHaveThisCollectionOf(array $blogs): AnyInvokedCount
    {
        $this->adSpaceRepository
            ->method('findAll')
            ->willReturn($blogs);

        $this->proposalBuilder
            ->method('calculateProposal')
            ->willReturn(new Proposal(123));

        $this->publisher
            ->expects($publisherSpy = self::any())
            ->method('publish');

        return $publisherSpy;
    }

    private function thenWePublishProposalsForEachBlog(array $blogs, AnyInvokedCount $publisherSpy): void
    {
        self::assertEquals(count($blogs), $publisherSpy->getInvocationCount());
    }

    private function whenWeSendAllQuotes(): void
    {
        $this->bot->sendAllQuotes('FAST');
    }

}
