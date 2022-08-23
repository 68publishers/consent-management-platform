<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Schema;
use App\Application\DataProcessor\Context\ContextInterface;

interface DescriptorInterface
{
	/**
	 * @param \App\Application\DataProcessor\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Schema
	 */
	public function schema(ContextInterface $context): Schema;
}
