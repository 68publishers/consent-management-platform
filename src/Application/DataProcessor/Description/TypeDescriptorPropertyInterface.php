<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataProcessor\Context\ContextInterface;

interface TypeDescriptorPropertyInterface
{
	/**
	 * @param \Nette\Schema\Elements\Type                             $type
	 * @param \App\Application\DataProcessor\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Type
	 */
	public function applyToType(Type $type, ContextInterface $context): Type;
}
