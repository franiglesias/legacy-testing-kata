<?php

class UpdatedMarketStudyVendor
{
    public function averagePrice(string $blog): float
    {
        if (empty(getenv("license"))) {
            throw new RuntimeException("[Stupid license] Missing license!!!!");
        }

        return random_int(50000, 100000);
    }
}
