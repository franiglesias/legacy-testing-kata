<?php
declare(strict_types=1);

namespace Quotebot\Tests\E2e\Doubles;


use Quotebot\Domain\Publisher;

class PublisherSpy implements Publisher
{
	private array $proposals = [];

	public function publishProposal($proposal): void
	{
		$this->proposals[] = $proposal;
	}

	public function proposals(): array
	{
		return $this->proposals;
	}
}
