<?php


namespace Quotebot\Domain\AdSpace;


abstract class AdSpace
{
    private $name;
    private $id;

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

}