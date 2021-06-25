<?php
declare(strict_types=1);

namespace Quotebot\Domain;

interface MarketDataProvider
{
	public function averagePrice(string $blog): float;
}
