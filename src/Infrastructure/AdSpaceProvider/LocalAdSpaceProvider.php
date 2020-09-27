<?php


namespace Quotebot\Infrastructure\AdSpaceProvider;


use Quotebot\Domain\AdSpace\Blog;
use Quotebot\Domain\AdSpaceProvider;

class LocalAdSpaceProvider implements AdSpaceProvider
{

    public function getSpaces(): array
    {
        return [
            new Blog('TalkingBit'),
            new Blog('La semana PHP')
        ];
    }

    public function findSpaces(callable $specification): array
    {
        $spaces = $this->getSpaces();

        return array_filter($spaces, $specification);
    }
}