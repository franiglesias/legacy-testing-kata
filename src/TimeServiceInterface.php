<?php
declare (strict_types=1);

namespace Quotebot;

interface TimeServiceInterface
{

    public function getTimestamp($date);

    public function getTodayTimestamp();
}
