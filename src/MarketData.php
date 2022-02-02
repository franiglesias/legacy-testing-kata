<?php

declare(strict_types=1);

namespace Quotebot;


interface MarketData
{
	public function averagePrice($blog): float;
}
