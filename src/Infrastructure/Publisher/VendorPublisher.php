<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\Publisher;

use Quotebot\Domain\Proposal;
use Quotebot\Domain\Publisher;

class VendorPublisher implements Publisher
{

    public function publish(Proposal $proposal): void
    {
        \QuotePublisher::publish($proposal->amount());
    }
}
