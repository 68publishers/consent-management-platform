<?php

declare(strict_types=1);

namespace App\Application\DataReader\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataReader\Context\ContextInterface;

final class Assert implements TypeDescriptorPropertyInterface
{
	/** @var callable  */
	private $validator;

	private ?string $description;

	/**
	 * @param callable    $validator
	 * @param string|NULL $description
	 */
	public function __construct(callable $validator, ?string $description = NULL)
	{
		$this->validator = $validator;
		$this->description = $description;
	}

	/**
	 * {@inheritDoc}
	 */
	public function applyToType(Type $type, ContextInterface $context): Type
	{
		return $type->assert($this->validator, $this->description);
	}
}
