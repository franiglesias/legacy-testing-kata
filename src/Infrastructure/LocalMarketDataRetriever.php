<?php


namespace Quotebot\Infrastructure;


class LocalMarketDataRetriever implements \Quotebot\Domain\MarketDataRetriever
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