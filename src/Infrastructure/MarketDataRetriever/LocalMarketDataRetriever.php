<?php


namespace Quotebot\Infrastructure\MarketDataRetriever;


use Quotebot\Domain\MarketDataRetriever;

class LocalMarketDataRetriever implements MarketDataRetriever
{

    public function averagePrice(string $blog): float
    {
        $blogAvgPrices = [
            'TalkingBit' => 1000,
            'La semana PHP' => 1500,
        ];
        return $blogAvgPrices[$blog];
    }
}