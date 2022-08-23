<?php

declare(strict_types=1);

namespace App\Application\DataProcessor\Description;

use Nette\Schema\Elements\Type;
use App\Application\DataProcessor\Context\ContextInterface;

final class BooleanDescriptor extends AbstractTypeDescriptor
{
	/**
	 * @param \App\Application\DataProcessor\Context\ContextInterface $context
	 *
	 * @return \Nette\Schema\Elements\Type
	 */
	protected function createType(ContextInterface $context): Type
	{
		$type = new Type('boolean');

		if (TRUE === ($context[ContextInterface::WEAK_TYPES] ?? FALSE)) {
			$type->before(function ($value) {
				$value = $this->tryToConvertWeakNullValue($value);

				if (NULL === $value || is_bool($value)) {
					return $value;
				}

				$val = $value;

				if (is_string($val)) {
					$val = trim($val);
				}

				if (in_array($val, [1, '1', 't', 'true', 'TRUE'], TRUE)) {
					$value = TRUE;
				}

				if (in_array($val, [0, '0', 'f', 'false', 'FALSE'], TRUE)) {
					$value = FALSE;
				}

				return $value;
			});
		}

		return $type;
	}
}
