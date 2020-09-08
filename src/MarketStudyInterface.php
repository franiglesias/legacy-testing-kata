<?php
declare (strict_types=1);

namespace Quotebot;

interface MarketStudyInterface
{
    public function averagePrice(string $blog): float;
}
