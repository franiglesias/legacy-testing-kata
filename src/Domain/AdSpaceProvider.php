<?php


namespace Quotebot\Domain;


interface AdSpaceProvider
{
    public function getSpaces(): array;
}