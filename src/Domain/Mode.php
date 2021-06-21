<?php
declare(strict_types=1);

namespace Quotebot\Domain;


class Mode
{

	private string $mode;

	private const SLOW      = 'SLOW';
	private const MEDIUM    = 'MEDIUM';
	private const FAST      = 'FAST';
	private const ULTRAFAST = 'ULTRAFAST';
	private const UNKNOWN   = 'UNKNOWN';

	private const TIME_FACTOR = [
		self::UNKNOWN   => 1,
		self::SLOW      => 2,
		self::MEDIUM    => 4,
		self::FAST      => 8,
		self::ULTRAFAST => 13,
	];

	public function __construct(string $mode)
	{
		$mode = strtoupper($mode);

		if ($this->modelRawValueIsKnown($mode)) {
			$mode = self::UNKNOWN;
		}

		$this->mode = $mode;
	}

	public function timeFactor(): int
	{
		return self::TIME_FACTOR[$this->mode];
	}

	protected function modelRawValueIsKnown(string $mode): bool
	{
		$knownValues = array_keys(self::TIME_FACTOR);

		return !in_array($mode, $knownValues, true);
	}

	public function __toString(): string
	{
		return $this->mode;
	}
}
