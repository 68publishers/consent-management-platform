<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataReader\Context\ContextInterface;

final class Deprecated implements TypeDescriptorPropertyInterface
{
	private string $message;

	/**
	 * @param string $message
	 */
	public function __construct(string $message)
	{
		$this->message = $message;
	}

	/**
	 * {@inheritDoc}
	 */
	public function applyToType(Type $type, ContextInterface $context): Type
	{
		return $type->deprecated($this->message);
	}
}
