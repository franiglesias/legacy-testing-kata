<?php


namespace Quotebot\Infrastructure;


use Quotebot\Domain\AdSpaceProvider;

class LocalAdSpaceProvider implements AdSpaceProvider
{

    public function getSpaces()
    {
        return [
            'TalkingBit',
            'La semana PHP'
        ];
    }
}