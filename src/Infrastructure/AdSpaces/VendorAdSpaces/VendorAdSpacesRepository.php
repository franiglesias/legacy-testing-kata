<?php
declare (strict_types=1);

namespace Quotebot\Infrastructure\AdSpaces\VendorAdSpaces;

use Quotebot\Domain\AdSpaceRepository;
use Quotebot\Domain\Blog;

class VendorAdSpacesRepository implements AdSpaceRepository
{
    public function findAll(): array
    {
        $adSpaces = AdSpace::getAdSpaces();

        return array_map(static fn(string $blogName) => new Blog($blogName), $adSpaces);
    }
}
