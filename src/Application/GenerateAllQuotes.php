<?php


namespace Quotebot\Application;


class GenerateAllQuotes
{

    /** @var string */
    private $rawMode;
    /** @var callable */
    private $specification;

    public function __construct(
        string $rawMode,
        callable $specification)
    {
        $this->rawMode = $rawMode;
        $this->specification = $specification;
    }

    public function getRawMode(): string
    {
        return $this->rawMode;
    }

    public function getSpecification(): callable
    {
        return $this->specification;
    }
}