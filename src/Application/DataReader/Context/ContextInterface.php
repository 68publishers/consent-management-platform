<?php

declare(strict_types=1);

namespace App\Application\DataReader\Context;

use ArrayAccess;

interface ContextInterface extends ArrayAccess
{
	public const WEAK_TYPES = 'weak_types';

	/**
	 * @param array $array
	 *
	 * @return static
	 */
	public static function default(array $array): self;

	/**
	 * @param array $array
	 *
	 * @return static
	 */
	public static function fromArray(array $array): self;
}
