<?php
declare(strict_types=1);

namespace Quotebot\Tests\Domain;

use PHPUnit\Framework\TestCase;
use Quotebot\BlogAuctionTask;
use Quotebot\Domain\CalculateProposal;
use Quotebot\Domain\ClockService;
use Quotebot\Domain\MarketDataProvider;
use Quotebot\Domain\Mode;
use Quotebot\Domain\Publisher;
use Quotebot\Tests\E2e\Doubles\PublisherSpy;

class BlogAuctionTaskTest extends TestCase
{
	private MarketDataProvider $marketDataProvider;
	private Publisher $publisher;
	private ClockService $clockService;
	private BlogAuctionTask $blogAuctionTask;

	protected function setUp(): void
	{
		$this->marketDataProvider = $this->createMock(MarketDataProvider::class);
		$this->publisher          = new PublisherSpy;
		$this->clockService       = $this->createMock(ClockService::class);

		$calculateProposal = new CalculateProposal($this->clockService);

		$this->blogAuctionTask    = new BlogAuctionTask(
			$this->marketDataProvider,
			$this->publisher,
			$calculateProposal
		);
	}

	/** @test
	 * @dataProvider examplesProvider
	 */
	public function shouldGenerateAProposal(float $averagePrice, int $timestampDiff, float $proposalAmount): void
	{
		$this->marketDataProvider->method('averagePrice')->willReturn($averagePrice);
		$this->clockService->method('timestampDiff')->willReturn($timestampDiff);

		$this->blogAuctionTask->priceAndPublish('A blog', new Mode('SLOW'));

		$this->assertProposalAmount($proposalAmount);
	}

	public function examplesProvider(): array
	{
		return [
			'Odd method' => [0.0, 1, 6.28],
			'Even method' => [1.0, 1, 6.30]
		];
	}

	protected function assertProposalAmount(float $proposalAmount): void
	{
		$proposals = $this->publisher->proposals();

		self::assertEquals($proposalAmount, $proposals[0]);
	}
}
