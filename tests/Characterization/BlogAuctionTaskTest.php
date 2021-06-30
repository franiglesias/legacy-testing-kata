<?php
declare (strict_types=1);

namespace Quotebot\Tests\Characterization;

use Quotebot\BlogAuctionTask;
use PHPUnit\Framework\TestCase;
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
        $blogAuctionTask = new class($averagePrice) extends BlogAuctionTask {
            private $proposal;
            private $averagePrice;

            public function __construct(float $averagePrice)
            {
                $this->averagePrice = $averagePrice;
            }

            protected function averagePrice(string $blog): float
            {
                return $this->averagePrice;
            }

            protected function timeDiff(string $fromDate): int
            {
                return 1;
            }

            protected function publishProposal($proposal): void
            {
                $this->proposal = $proposal;
            }

            public function proposal()
            {
                return $this->proposal;
            }
        };

        return $blogAuctionTask;
    }

}
