<?php

declare(strict_types=1);

namespace Quotebot\Tests;

use ApprovalTests\Approvals;
use PHPUnit\Framework\TestCase;
use Quotebot\AutomaticQuoteBot;
use Quotebot\Clock;
use Quotebot\MarketData;
use Quotebot\Publisher;

final class AutomaticQuoteBotTest extends TestCase
{
	/** @test */
	public function shouldRun(): void
	{
		$marketData = $this->marketData();
		$publisher  = $this->publisher();
		$clock      = $this->clock();

		$bot = new AutomaticQuoteBot(
			$marketData,
			$publisher,
			$clock
		);

		$modes = ['FAST', 'ULTRAFAST', 'SLOW', 'MEDIUM', 'UNKNOWN'];

		foreach ($modes as $mode) {
			$bot->sendAllQuotes($mode);
		}

		Approvals::verifyList($publisher->proposals());

	}

	private function marketData(): MarketData
	{
		return new class() implements MarketData {

			public function averagePrice($blog): float
			{
				$blogs = [
					'HackerNews'  => 105.0,
					'Reddit'      => 129.0,
					'TechCrunch'  => 143.0,
					'BuzzFeed'    => 120.0,
					'TMZ'         => 140.0,
					'TheHuffPost' => 90.0,
					'GigaOM'      => 75.0,
				];

				return $blogs[$blog] ?? 100.0;
			}
		};
	}

	private function publisher(): Publisher
	{
		return new class() implements Publisher {
			private $proposals = [];

			public function publish($proposal): void
			{
				$this->proposals[] = $proposal;
			}

			public function proposals(): array
			{
				return $this->proposals;
			}
		};
	}

	private function clock(): Clock
	{
		return new class() implements Clock {

			public function timeDiff(string $fromDate): int
			{
				return 10;
			}
		};
	}

}
