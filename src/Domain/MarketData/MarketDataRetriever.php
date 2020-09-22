<?php

namespace Quotebot\Domain\MarketData;

interface MarketDataRetriever
{
    public function averagePrice(string $blog): Price;
}