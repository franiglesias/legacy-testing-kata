<?php


namespace Quotebot\Domain;


class Price
{
    /**
     * @var float
     */
    private $price;

    /**
     * Price constructor.
     */
    public function __construct(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
}