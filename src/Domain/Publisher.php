<?php
declare (strict_types=1);

namespace Quotebot\Domain;

interface Publisher
{
    public function publish($proposal): void;
}
