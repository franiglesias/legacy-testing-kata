<?php


namespace Quotebot\Infrastructure;


use MarketStudyVendor;
use Quotebot\Domain\MarketDataRetriever;

class VendorDataRetriever implements MarketDataRetriever
{
    /**
     * @var MarketStudyVendor
     */
    private $marketStudyVendor;

    public function __construct(MarketStudyVendor $marketStudyVendor)
    {
        $this->marketStudyVendor = $marketStudyVendor;
    }

    public function averagePrice(string $blog): float
    {
        return $this->marketStudyVendor->averagePrice($blog);
    }
}