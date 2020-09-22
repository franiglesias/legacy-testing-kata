<?php


namespace Quotebot\Infrastructure\MarketDataRetriever;


use MarketStudyVendor;
use Quotebot\Domain\MarketDataRetriever;
use Quotebot\Domain\Price;

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

    public function averagePrice(string $blog): Price
    {
        $averagePrice = $this->marketStudyVendor->averagePrice($blog);

        return new Price($averagePrice);
    }
}