<?php


namespace Quotebot\Infrastructure\AdSpaceProvider;


use Quotebot\Domain\AdSpaceProvider;

class BlogAdSpaceProvider implements AdSpaceProvider
{

    public function getSpaces()
    {
        return AdSpace::getAdSpaces();
    }
}