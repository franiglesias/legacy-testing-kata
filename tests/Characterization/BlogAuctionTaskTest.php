<?php
declare (strict_types=1);

namespace Quotebot\Tests\Characterization;

use Quotebot\BlogAuctionTask;
use PHPUnit\Framework\TestCase;
use Quotebot\Domain\Blog;
use Quotebot\Domain\MarketStudyProvider;
use Quotebot\Domain\Proposal;
use Quotebot\Domain\Publisher;
use RuntimeException;

class BlogAuctionTaskTest extends TestCase
{
    /** @test
     * @dataProvider examplesProvider
     */
    public function shouldGenerateAProposal(string $mode, float $averagePrice, float $proposal): void
    {
        $blogAuctionTask = $this->getBlogAuctionTask($averagePrice);

        $blog = 'Blog Example';

        $blogAuctionTask->priceAndPublish($blog, $mode);

        self::assertEquals($proposal, $blogAuctionTask->proposal());
    }

    public function examplesProvider(): array
    {
        return [
            'odd slow' => ['SLOW', 0.0, 6.28],
            'even slow' => ['SLOW', 1.0, 6.3],
            'odd medium' => ['MEDIUM', 0.0, 6.28],
            'even medium' => ['MEDIUM', 1.0, 12.6],
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

        $publisher = new class() implements Publisher {
            private Proposal $proposal;

            public function publish(Proposal $proposal): void
            {
                $this->proposal = $proposal;
            }

            public function proposal(): float
            {
                return $this->proposal->amount();
            }
        };

        $blogAuctionTask = new class($marketStudyProvider, $publisher) extends BlogAuctionTask {

            protected function timeDiff(string $fromDate): int
            {
                return 1;
            }

            public function proposal()
            {
                return $this->publisher->proposal();
            }
        };

        return $blogAuctionTask;
    }

}
