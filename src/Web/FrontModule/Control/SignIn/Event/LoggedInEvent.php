<?php

declare(strict_types=1);

namespace App\Web\FrontModule\Control\SignIn\Event;

use Symfony\Contracts\EventDispatcher\Event;
use SixtyEightPublishers\UserBundle\ReadModel\View\IdentityView;

final class LoggedInEvent extends Event
{
	private IdentityView $identity;

	/**
	 * @param \SixtyEightPublishers\UserBundle\ReadModel\View\IdentityView $identity
	 */
	public function __construct(IdentityView $identity)
	{
		$this->identity = $identity;
	}

	/**
	 * @return \SixtyEightPublishers\UserBundle\ReadModel\View\IdentityView
	 */
	public function identity(): IdentityView
	{
		return $this->identity;
	}
}
