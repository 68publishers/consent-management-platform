<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataReader\Context\ContextInterface;

final class StringDescriptor extends AbstractTypeDescriptor
{
	/**
	 * @param \App\Application\DataReader\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Type
	 */
	protected function createType(ContextInterface $context): Type
	{
		return new Type('string');
	}
}
