<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Schema;
use App\Application\DataProcessor\Description\Path\Path;
use App\Application\DataProcessor\Context\ContextInterface;
use App\Application\DataProcessor\Description\Path\PathInfo;

interface DescriptorInterface
{
	/**
	 * @param \App\Application\DataProcessor\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Schema
	 */
	public function schema(ContextInterface $context): Schema;

	/**
	 * @param \App\Application\DataProcessor\Description\Path\Path $path
	 *
	 * @return \App\Application\DataProcessor\Description\Path\PathInfo
	 */
	public function pathInfo(Path $path): PathInfo;
}
