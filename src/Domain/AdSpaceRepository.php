<?php
declare (strict_types=1);

namespace Quotebot\Domain;

interface AdSpaceRepository
{
    public function findAll(): array;
}
