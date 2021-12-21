<?php

declare (strict_types=1);

namespace Quotebot\Domain;

class Printer
{
    public function print(Proposal $proposal): void
    {
        printf('Proposal %s', $proposal->amount());
    }
}