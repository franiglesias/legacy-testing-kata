<?php


namespace Quotebot\Domain;


interface TimeService
{
    public function timeInterval(): int;
}