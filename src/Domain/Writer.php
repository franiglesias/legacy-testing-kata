<?php

declare (strict_types=1);

namespace Quotebot\Domain;

interface Writer
{
    public function line(string $theLine): void;
}