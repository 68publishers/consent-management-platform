<?php

declare(strict_types=1);

namespace App\Domain\Consent\ValueObject;

use SixtyEightPublishers\ArchitectureBundle\Domain\ValueObject\AbstractArrayValueObject;

final class Consents extends AbstractArrayValueObject
{
	public function positiveCount(array $categoryCodes): int
	{
		return $this->calculateCount($categoryCodes, TRUE);
	}

	public function negativeCount(array $categoryCodes): int
	{
		return $this->calculateCount($categoryCodes, FALSE);
	}

	private function calculateCount(array $categoryCodes, bool $positive): int
	{
		return count(
			array_filter(
				$this->values(),
				static fn (bool $value, string $key) => in_array($key, $categoryCodes, TRUE) && $positive === $value,
				ARRAY_FILTER_USE_BOTH
			)
		);
	}
}
