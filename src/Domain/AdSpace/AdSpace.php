<?php


namespace Quotebot\Domain\AdSpace;


abstract class AdSpace
{
    protected $name;
    protected $id;

    public function __construct(string $name)
    {
        $this->id = $name;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function startsWith(string $start): bool
    {
        return strpos($this->name, $start, 0) === 0;
    }
}