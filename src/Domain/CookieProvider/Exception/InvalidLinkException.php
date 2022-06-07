<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\Exception;

use DomainException;

final class InvalidLinkException extends DomainException
{
	/**
	 * @param string $message
	 */
	private function __construct(string $message)
	{
		parent::__construct($message);
	}

	/**
	 * @param string $link
	 *
	 * @return static
	 */
	public static function invalidUrl(string $link): self
	{
		return new self(sprintf(
			'Link %s is not valid url.',
			$link
		));
	}
}
