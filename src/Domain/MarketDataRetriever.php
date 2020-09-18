<?php

namespace Quotebot\Domain;

interface MarketDataRetriever
{
    public function averagePrice(string $blog): float;
}