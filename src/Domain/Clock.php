<?php
declare (strict_types=1);

namespace Quotebot\Domain;

interface Clock
{

    public function secondsSince(string $fromDate): int;
}
