<?php
declare (strict_types=1);

namespace Quotebot\Tests\Infrastructure\MarketStudyProvider;

use PHPUnit\Framework\TestCase;
use Quotebot\Domain\Blog;
use Quotebot\Infrastructure\MarketStudyProvider\MarketStudyVendorAdapter;

class MarketStudyVendorAdapterTest extends TestCase
{
    /** @test */
    public function shouldDelegateToMarketStudyVendor(): void
    {
        $marketStudyVendor = $this->createMock(\MarketStudyVendor::class);
        $marketStudyVendor->method('averagePrice')
                          ->with('Talking Bit')
                          ->willReturn(40.0);

        $adapter = new MarketStudyVendorAdapter($marketStudyVendor);

        $blog = new Blog('Talking Bit');
        self::assertEquals(40.0, $adapter->averagePrice($blog));
    }

}
