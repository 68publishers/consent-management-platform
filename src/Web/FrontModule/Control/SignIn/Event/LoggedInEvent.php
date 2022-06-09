<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn\Event;

use App\ReadModel\User\UserView;
use Symfony\Contracts\EventDispatcher\Event;

final class LoggedInEvent extends Event
{
	private UserView $userView;

	/**
	 * @param \App\ReadModel\User\UserView $userView
	 */
	public function __construct(UserView $userView)
	{
		$this->userView = $userView;
	}

	/**
	 * @return \App\ReadModel\User\UserView
	 */
	public function userView(): UserView
	{
		return $this->userView;
	}
}
