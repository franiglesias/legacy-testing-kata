<?php
declare (strict_types=1);

namespace Quotebot\Tests\Infrastructure\EntryPoint;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Quotebot\Infrastructure\EntryPoint\Application;

class ApplicationTest extends TestCase
{
    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function shouldRunInTestWithoutCallingProductionVendors(): void
    {
        Application::main(['APP_ENV' => 'test']);
    }

    /**
     * @test
     * @doesNotPerformAssertions
     */
    public function shouldRunDefinedEnvironment(): void
    {
        vfsStream::setup('quotebot', null, [
            '.env' => 'APP_ENV=dev'
        ]);
        $path = vfsStream::url('quotebot/');

        Application::main(['BASE_PATH' => $path]);
    }

}
