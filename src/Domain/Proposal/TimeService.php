<?php


namespace Quotebot\Domain\Proposal;


interface TimeService
{
    public function timeInterval(): int;
}