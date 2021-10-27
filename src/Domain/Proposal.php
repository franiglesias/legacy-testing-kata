<?php
declare (strict_types=1);

namespace Quotebot\Domain;

class Proposal
{

    private float $amount;

    public function __construct(float $amount)
    {
        $this->amount = $amount;
    }

    public function amount(): float
    {
        return $this->amount;
    }
}
