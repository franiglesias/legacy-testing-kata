<?php
declare (strict_types=1);

namespace Quotebot\Tests\Characterization;

use Quotebot\BlogAuctionTask;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class BlogAuctionTaskTest extends TestCase
{
    /** @test */
    public function shouldRun(): void
    {
        $blogAuctionTask = $this->getBlogAuctionTask();

        $blog = 'Blog Example';
        $mode = 'SLOW';

        $blogAuctionTask->priceAndPublish($blog, $mode);

        self::assertEquals(6.3, $blogAuctionTask->proposal());
    }

    private function getBlogAuctionTask(): BlogAuctionTask
    {
        $blogAuctionTask = new class() extends BlogAuctionTask {
            private $proposal;

            protected function averagePrice(string $blog): float
            {
                return 0.0;
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
