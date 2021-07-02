<?php
declare (strict_types=1);

namespace Quotebot\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Quotebot\Domain\Mode;

class ModeTest extends TestCase
{

    /** @test
     * @dataProvider modesProvider
     */
    public function shouldProduceTimeFactor(string $mode, int $timeFactor): void
    {
        $mode = new Mode($mode);

        self::assertEquals($timeFactor, $mode->timeFactor());
    }

    public function modesProvider(): array
    {
        return [
            'Unknown mode' => ['Unknown', 1],
            'Slow mode' => ['SLOW', 2],
            'Medium mode' => ['MEDIUM', 4],
            'Fast mode' => ['FAST', 8],
            'Ultrafast mode' => ['ULTRAFAST', 13],
            'Slow mode 2' => ['slow', 2]
        ];
    }
}
