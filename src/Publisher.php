<?php
declare (strict_types=1);

namespace Quotebot;

class Publisher implements PublisherInterface
{

    public function publish($proposal): void
    {
        \QuotePublisher::publish($proposal);
    }
}
