<?php
declare (strict_types=1);

namespace Quotebot\Domain;

class Mode
{

    private $mode;

    public function __construct(string $mode)
    {
        $this->mode = $mode;
    }

    public function timeFactor(): int
    {
        $timeFactor = 1;

        if ($this->mode === 'SLOW') {
            $timeFactor = 2;
        }

        if ($this->mode === 'MEDIUM') {
            $timeFactor = 4;
        }

        if ($this->mode === 'FAST') {
            $timeFactor = 8;
        }

        if ($this->mode === 'ULTRAFAST') {
            $timeFactor = 13;
        }

        return $timeFactor;
    }
}
