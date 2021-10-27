<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\Publisher;

use Quotebot\Domain\Publisher;

class VendorPublisher implements Publisher
{

    public function publish($proposal): void
    {
        \QuotePublisher::publish($proposal);
    }
}
