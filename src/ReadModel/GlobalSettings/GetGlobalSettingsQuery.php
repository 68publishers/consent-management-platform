<?php

declare(strict_types=1);

namespace App\ReadModel\GlobalSettings;

use SixtyEightPublishers\ArchitectureBundle\ReadModel\Query\AbstractQuery;

/**
 * Returns GlobalSettingsView or NULL
 */
final class GetGlobalSettingsQuery extends AbstractQuery
{
	/**
	 * @return static
	 */
	public static function create(): self
	{
		return self::fromParameters([]);
	}
}
