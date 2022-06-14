<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CookieForm\Event;

use App\Domain\Cookie\ValueObject\CookieId;
use Symfony\Contracts\EventDispatcher\Event;

final class CookieCreatedEvent extends Event
{
	private CookieId $cookieId;

	private string $name;

	/**
	 * @param \App\Domain\Cookie\ValueObject\CookieId $cookieId
	 * @param string                                  $name
	 */
	public function __construct(CookieId $cookieId, string $name)
	{
		$this->cookieId = $cookieId;
		$this->name = $name;
	}

	/**
	 * @return \App\Domain\Cookie\ValueObject\CookieId
	 */
	public function cookieId(): CookieId
	{
		return $this->cookieId;
	}

	/**
	 * @return string
	 */
	public function name(): string
	{
		return $this->name;
	}
}
