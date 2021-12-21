<?php

declare (strict_types=1);

namespace Quotebot\Domain;

interface Printer
{
    public function print(Proposal $proposal): void;
}