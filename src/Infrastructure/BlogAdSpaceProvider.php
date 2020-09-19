<?php


namespace Quotebot\Infrastructure;


use Quotebot\AdSpace;
use Quotebot\Domain\AdSpaceProvider;

class BlogAdSpaceProvider implements AdSpaceProvider
{

    public function getSpaces()
    {
        return AdSpace::getAdSpaces();
    }
}