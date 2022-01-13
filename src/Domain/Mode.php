<?php
declare (strict_types=1);

namespace Quotebot\Domain;

class Mode
{
    private const SLOW = 'SLOW';
    private const MEDIUM = 'MEDIUM';
    private const FAST = 'FAST';
    private const ULTRAFAST = 'ULTRAFAST';
    private const UNKNOWN = 'UNKNOWN';

    private const TIME_FACTOR_MAP = [
        self::UNKNOWN => 1,
        self::SLOW => 2,
        self::MEDIUM => 4,
        self::FAST => 8,
        self::ULTRAFAST => 13,
    ];

    private string $mode;

    public function __construct(string $mode)
    {
        $mode = strtoupper($mode);

        $this->mode = !isset(self::TIME_FACTOR_MAP[$mode])
            ? self::UNKNOWN
            : $mode;
    }

    public function timeFactor(): int
    {
        return self::TIME_FACTOR_MAP[$this->mode];
    }

    public function __toString(): string
    {
        return $this->mode;
    }
}
