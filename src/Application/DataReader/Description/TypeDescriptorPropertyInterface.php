<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataReader\Context\ContextInterface;

interface TypeDescriptorPropertyInterface
{
	/**
	 * @param \Nette\Schema\Elements\Type                          $type
	 * @param \App\Application\DataReader\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Type
	 */
	public function applyToType(Type $type, ContextInterface $context): Type;
}
