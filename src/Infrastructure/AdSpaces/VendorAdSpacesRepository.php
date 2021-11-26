<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\AdSpaces;

use Quotebot\AdSpace;
use Quotebot\Domain\AdSpaceRepository;

class VendorAdSpacesRepository implements AdSpaceRepository
{
    public function findAll(): array
    {
        return AdSpace::getAdSpaces();
    }
}
