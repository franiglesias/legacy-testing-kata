<?php

namespace Quotebot\Domain\AdSpace;

use PHPUnit\Framework\TestCase;

class AdSpaceTest extends TestCase
{

    public function testStartsWith(): void
    {
        $space = new class('Example') extends AdSpace {
        } ;

        self::assertTrue($space->startsWith('E') );
        self::assertFalse($space->startsWith('T'));
    }
}
