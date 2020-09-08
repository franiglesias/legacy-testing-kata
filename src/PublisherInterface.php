<?php
declare (strict_types=1);

namespace Quotebot;

interface PublisherInterface
{
    public function publish($proposal): void;

}
