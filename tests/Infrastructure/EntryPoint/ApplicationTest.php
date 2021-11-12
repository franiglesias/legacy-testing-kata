<?php
declare (strict_types=1);

namespace Quotebot\Tests\Infrastructure\EntryPoint;

use Quotebot\Infrastructure\EntryPoint\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function shouldRunWithoutCallingProductionVendors(): void
    {
        Application::main(['APP_ENV' => 'test']);
    }

}
