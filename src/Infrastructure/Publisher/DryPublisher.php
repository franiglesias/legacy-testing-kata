<?php

declare (strict_types=1);

namespace Quotebot\Infrastructure\Publisher;

use Quotebot\Domain\Proposal;
use Quotebot\Domain\Publisher;

class DryPublisher implements Publisher
{

    public function publish(Proposal $proposal): void
    {
        print ('Quote won\'t be sent' . PHP_EOL);
    }
}