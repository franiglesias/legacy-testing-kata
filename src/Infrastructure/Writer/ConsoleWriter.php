<?php

declare (strict_types=1);

namespace Quotebot\Infrastructure\Writer;

use Quotebot\Domain\Writer;

class ConsoleWriter implements Writer
{

    public function line(string $theLine): void
    {
        print($theLine . PHP_EOL);
    }
}