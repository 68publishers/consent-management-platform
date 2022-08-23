<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataProcessor\Context\ContextInterface;

final class IntegerDescriptor extends AbstractTypeDescriptor
{
	/**
	 * @param \App\Application\DataProcessor\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Type
	 */
	protected function createType(ContextInterface $context): Type
	{
		$type = new Type('integer');

		if (TRUE === ($context[ContextInterface::WEAK_TYPES] ?? FALSE)) {
			$type->before(function ($value) {
				$value = $this->tryToConvertWeakNullValue($value);

				if (!is_string($value)) {
					return $value;
				}

				$val = trim($value);

				if (TRUE === (bool) preg_match('/^\d+$/', $val)) {
					$value = (int) $val;
				}

				return $value;
			});
		}

		return $type;
	}
}
