<?php

declare(strict_types=1);

namespace Quotebot;


interface Publisher
{

	public function publish($proposal): void;
}
