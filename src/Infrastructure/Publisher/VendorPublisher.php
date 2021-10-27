<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\Publisher;

class VendorPublisher
{

    public function publish($proposal): void
    {
        \QuotePublisher::publish($proposal);
    }
}
