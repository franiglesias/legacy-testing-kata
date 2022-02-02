<?php

declare(strict_types=1);

namespace Quotebot;


final class VendorMarketData implements MarketData
{
	private \MarketStudyVendor $marketStudyVendor;

	public function __construct(\MarketStudyVendor $marketStudyVendor)
	{
		$this->marketStudyVendor = $marketStudyVendor;
	}

	public function averagePrice($blog): float
	{
		return $this->marketStudyVendor->averagePrice($blog);
	}
}
