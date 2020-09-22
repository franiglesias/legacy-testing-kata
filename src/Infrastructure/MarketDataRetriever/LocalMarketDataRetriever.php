<?php


namespace Quotebot\Infrastructure\MarketDataRetriever;


use Quotebot\Domain\MarketDataRetriever;
use Quotebot\Domain\Price;

class LocalMarketDataRetriever implements MarketDataRetriever
{

    public function averagePrice(string $blog): Price
    {
        $blogAvgPrices = [
            'TalkingBit' => 1000,
            'La semana PHP' => 1500,
        ];
        return new Price($blogAvgPrices[$blog]);
    }
}