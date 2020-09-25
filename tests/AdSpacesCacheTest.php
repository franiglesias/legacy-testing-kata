<?php

namespace Tests\Quotebot;

use PHPUnit\Framework\TestCase;
use Quotebot\Infrastructure\AdSpaceProvider\AdSpacesCache;

class AdSpacesCacheTest extends TestCase
{

    public function testShouldBeEmpty(): void
    {
        $result = AdSpacesCache::getAdSpaces('blogs');

        self::assertEmpty($result);
    }

    public function testShouldCache(): void
    {
        $elements = [
            'Element 1',
            'Element 2'
        ];

        AdSpacesCache::cache('blogs', $elements);

        $stored = AdSpacesCache::getAdSpaces('blogs');

        self::assertEquals($elements, $stored);
    }
}
