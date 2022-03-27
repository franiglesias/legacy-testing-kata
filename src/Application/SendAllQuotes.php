<?php
declare(strict_types=1);

namespace Quotebot\Application;


class SendAllQuotes
{
	private string $rawMode;

	public function __construct(string $rawMode)
	{
		$this->rawMode = $rawMode;
	}

	public function getRawMode(): string
	{
		return $this->rawMode;
	}
}
