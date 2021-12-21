<?php

declare (strict_types=1);

namespace Quotebot\Infrastructure\Printer;

use Quotebot\Domain\Printer;
use Quotebot\Domain\Proposal;

class ConsolePrinter implements Printer
{
    public function print(Proposal $proposal): void
    {
        printf('Proposal %s', $proposal->amount());
    }
}