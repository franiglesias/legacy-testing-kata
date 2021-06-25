<?php
declare(strict_types=1);

namespace Quotebot\Application;


class SendAllQuotes
{
	private string $mode;

	public function __construct(string $mode)
	{
		$this->mode = $mode;
	}

	public function getMode(): string
	{
		return $this->mode;
	}
}
