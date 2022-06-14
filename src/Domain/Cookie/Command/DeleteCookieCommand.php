<?php

declare(strict_types=1);

namespace App\Domain\Cookie\Command;

use SixtyEightPublishers\ArchitectureBundle\Command\AbstractCommand;

final class DeleteCookieCommand extends AbstractCommand
{
	/**
	 * @param string $cookieId
	 *
	 * @return static
	 */
	public static function create(string $cookieId): self
	{
		return self::fromParameters([
			'cookie_id' => $cookieId,
		]);
	}

	/**
	 * @return string
	 */
	public function cookieId(): string
	{
		return $this->getParam('cookie_id');
	}
}
