<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Schema;
use App\Application\DataReader\Context\ContextInterface;

interface DescriptorInterface
{
	/**
	 * @param \App\Application\DataReader\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Schema
	 */
	public function getSchema(ContextInterface $context): Schema;
}
