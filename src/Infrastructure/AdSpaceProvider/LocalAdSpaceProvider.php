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
}