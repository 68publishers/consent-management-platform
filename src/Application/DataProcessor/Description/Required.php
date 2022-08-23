<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataProcessor\Context\ContextInterface;

final class Required implements TypeDescriptorPropertyInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function applyToType(Type $type, ContextInterface $context): Type
	{
		return $type->required();
	}
}
