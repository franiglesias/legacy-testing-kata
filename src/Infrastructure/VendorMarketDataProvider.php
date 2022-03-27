<?php
declare(strict_types=1);

namespace Quotebot\Infrastructure;


use Quotebot\Domain\MarketDataProvider;

class VendorMarketDataProvider implements MarketDataProvider
{
	private \MarketStudyVendor $marketStudyVendor;

	public function __construct(\MarketStudyVendor $marketStudyVendor)
	{
		$this->marketStudyVendor = $marketStudyVendor;
	}

	public function averagePrice(string $blog): float
	{
		return $this->marketStudyVendor->averagePrice($blog);
	}
}
