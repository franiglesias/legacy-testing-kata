<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\MarketStudyProvider;

use Quotebot\Domain\Blog;
use Quotebot\Domain\MarketStudyProvider;

class MarketStudyVendorAdapter implements MarketStudyProvider
{

    private \MarketStudyVendor $marketStudyVendor;

    public function __construct(\MarketStudyVendor $marketStudyVendor)
    {
        $this->marketStudyVendor = $marketStudyVendor;
    }

    public function averagePrice(Blog $blog): float
    {
        return $this->marketStudyVendor->averagePrice($blog->name());
    }
}
