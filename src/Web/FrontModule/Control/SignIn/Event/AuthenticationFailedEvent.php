<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn\Event;

use Symfony\Contracts\EventDispatcher\Event;
use SixtyEightPublishers\UserBundle\Application\Exception\AuthenticationException;

final class AuthenticationFailedEvent extends Event
{
	private AuthenticationException $exception;

	/**
	 * @param \SixtyEightPublishers\UserBundle\Application\Exception\AuthenticationException $exception
	 */
	public function __construct(AuthenticationException $exception)
	{
		$this->exception = $exception;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\Application\Exception\AuthenticationException
	 */
	public function exception(): AuthenticationException
	{
		return $this->exception;
	}
}
