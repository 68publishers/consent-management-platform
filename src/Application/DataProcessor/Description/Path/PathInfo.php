<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description\Path;

use App\Application\DataProcessor\Description\DescriptorInterface;

final class PathInfo
{
	public ?DescriptorInterface $descriptor;

	public bool $found;

	public bool $isFinal;
}
