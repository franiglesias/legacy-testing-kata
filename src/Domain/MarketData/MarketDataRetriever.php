<?php

namespace Quotebot\Domain\MarketData;

use Quotebot\Domain\AdSpace\AdSpace;

interface MarketDataRetriever
{
    public function averagePrice(AdSpace $blog): Price;
}