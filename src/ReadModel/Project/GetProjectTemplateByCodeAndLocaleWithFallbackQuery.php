<?php

declare(strict_types=1);

namespace App\ReadModel\Project;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

final class GetProjectTemplateByCodeAndLocaleWithFallbackQuery extends AbstractQuery
{
	/**
	 * @param string      $code
	 * @param string|NULL $locale
	 *
	 * @return static
	 */
	public static function create(string $code, ?string $locale = NULL): self
	{
		return self::fromParameters([
			'code' => $code,
			'locale' => $locale,
		]);
	}

	/**
	 * @return string
	 */
	public function code(): string
	{
		return $this->getParam('code');
	}

	/**
	 * @return string|NULL
	 */
	public function locale(): ?string
	{
		return $this->getParam('locale');
	}
}
