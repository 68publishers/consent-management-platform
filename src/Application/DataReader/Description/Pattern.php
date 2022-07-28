<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataReader\Context\ContextInterface;

final class Pattern implements TypeDescriptorPropertyInterface
{
	private string $pattern;

	/**
	 * @param string $pattern
	 */
	public function __construct(string $pattern)
	{
		$this->pattern = $pattern;
	}

	/**
	 * {@inheritDoc}
	 */
	public function applyToType(Type $type, ContextInterface $context): Type
	{
		return $type->pattern($this->pattern);
	}
}
