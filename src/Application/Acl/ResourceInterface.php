<?php

declare(strict_types=1);

namespace App\Application\Acl;

interface ResourceInterface
{
	/**
	 * @return string[]
	 */
	public static function privileges(): array;
}
