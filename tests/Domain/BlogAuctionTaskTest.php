<?php
declare (strict_types=1);

namespace Quotebot\Tests\Domain;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Quotebot\Domain\Blog;
use Quotebot\Domain\BlogAuctionTask;
use Quotebot\Domain\Clock;
use Quotebot\Domain\MarketStudyProvider;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Printer;
use Quotebot\Domain\Proposal;
use Quotebot\Domain\ProposalBuilder;
use Quotebot\Domain\Publisher;

class BlogAuctionTaskTest extends TestCase
{
    private Publisher $publisher;
    /** @var Printer|MockObject */
    private Printer $printer;

    protected function setUp(): void
    {
        $this->buildSpyablePublisher();
        $this->printer = $this->createMock(Printer::class);
    }

    /** @test
     * @dataProvider examplesProvider
     */
    public function shouldGenerateAProposal(Mode $mode, float $averagePrice, Proposal $proposal): void
    {
        $blog = new Blog('Blog Example');

        $this->printer
            ->expects($printerSpy = self::any())
            ->method('print')
            ->with($proposal);

        $blogAuctionTask = $this->getBlogAuctionTask($averagePrice);

        $blogAuctionTask->priceAndPublish($blog, $mode);

        self::assertEquals($proposal, $this->publisher->proposal());
        self::assertTrue($printerSpy->hasBeenInvoked());
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

    private function getBlogAuctionTask(float $averagePrice): BlogAuctionTask
    {
        $marketStudyProvider = new class($averagePrice) implements MarketStudyProvider {

            private float $averagePrice;

            public function __construct(float $averagePrice)
            {
                $this->averagePrice = $averagePrice;
            }

            public function averagePrice(Blog $blog): float
            {
                return $this->averagePrice;
            }
        };

        $clock = new class() implements Clock {

            public function secondsSince(string $fromDate): int
            {
                return 1;
            }
        };

        $proposalBuilder = new ProposalBuilder($marketStudyProvider, $clock);

        return new BlogAuctionTask($this->publisher, $proposalBuilder, $this->printer);
    }

    protected function buildSpyablePublisher(): void
    {
        $this->publisher = new class() implements Publisher {
            private Proposal $proposal;

            public function publish(Proposal $proposal): void
            {
                $this->proposal = $proposal;
            }

            public function proposal(): Proposal
            {
                return $this->proposal;
            }
        };
    }

}
