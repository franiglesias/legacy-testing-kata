<?php
declare(strict_types=1);

namespace Quotebot\Tests\Domain;

use Quotebot\Domain\Mode;
use PHPUnit\Framework\TestCase;

class ModeTest extends TestCase
{
	/** @test */
	public function shouldAllowUnknownModesWithNeutralTimeFactor(): void
	{
		$mode = new Mode('UNKNOWN');

		self::assertEquals(1, $mode->timeFactor());
	}

	/** @test */
	public function shouldBeCaseInsensitive(): void
	{
		$mode = new Mode('fast');

		self::assertEquals(8, $mode->timeFactor());
	}
}
