<?php
declare(strict_types=1);

namespace Quotebot\Infrastructure;


use Quotebot\AdSpace;
use Quotebot\Domain\GetAdSpaces;

class VendorGetAdSpaces implements GetAdSpaces
{

	public function all()
	{
		return AdSpace::getAdSpaces();
	}
}
