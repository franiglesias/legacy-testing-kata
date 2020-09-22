<?php


namespace Quotebot\Domain\Proposal;


use InvalidArgumentException;

class Mode
{
    private const SLOW = 'SLOW';
    private const MEDIUM = 'MEDIUM';
    private const FAST = 'FAST';
    private const ULTRAFAST = 'ULTRAFAST';

    private const MAP = [
        self::SLOW => 2,
        self::MEDIUM => 4,
        self::FAST => 8,
        self::ULTRAFAST => 13
    ];

    private $mode;

    public function __construct(string $mode)
    {
        $mode = strtoupper($mode);

        $validModes = array_keys(self::MAP);

        if (!in_array($mode, $validModes, true)) {
            throw new InvalidArgumentException('Invalid mode');
        }

        $this->mode = $mode;
    }

    public function timeFactor(): int
    {
        return self::MAP[$this->mode];
    }
}