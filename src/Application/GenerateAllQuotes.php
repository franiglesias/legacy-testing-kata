<?php


namespace Quotebot\Application;


class GenerateAllQuotes
{

    /**
     * @var string
     */
    private $rawMode;

    public function __construct(string $rawMode)
    {
        $this->rawMode = $rawMode;
    }

    public function getRawMode(): string
    {
        return $this->rawMode;
    }
}