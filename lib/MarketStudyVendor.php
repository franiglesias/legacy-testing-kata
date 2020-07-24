<?php

class MarketStudyVendor
{
    public function averagePrice(string $blog): float
    {
        if (empty(getenv("license"))) {
            throw new RuntimeException("[Stupid license] Missing license!!!!");
        }
        return (hashCode($blog) * (mt_rand() / mt_getrandmax()));
    }

}

// You don't need to touch this, it simulates the equivalent java

function hashCode($s)
{
    $h = 0;
    $len = strlen($s);
    for ($i = 0; $i < $len; $i++) {
        $h = overflow32(31 * $h + ord($s[$i]));
    }

    return $h;
}

function overflow32($v)
{
    $v %= 4294967296;
    if ($v > 2147483647) {
        return $v - 4294967296;
    } elseif ($v < -2147483648) {
        return $v + 4294967296;
    } else {
        return $v;
    }
}
