<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataReader\Context\ContextInterface;

final class DefaultValue implements TypeDescriptorPropertyInterface
{
	/** @var mixed */
	private $value;

	/**
	 * @param mixed $value
	 */
	public function __construct($value)
	{
		$this->value = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function applyToType(Type $type, ContextInterface $context): Type
	{
		return $type->default($this->value);
	}
}
