<?php


namespace Quotebot\Domain;


interface AdSpaceProvider
{
    public function getSpaces(): array;

    public function findSpaces(callable $specification): array;
}