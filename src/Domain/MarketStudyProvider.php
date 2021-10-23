<?php
declare (strict_types=1);

namespace Quotebot\Domain;

interface MarketStudyProvider
{
    public function averagePrice(Blog $blog);
}
