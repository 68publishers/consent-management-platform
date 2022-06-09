<?php

declare(strict_types=1);

namespace App\Web\Control\Localization\Event;

use App\Application\Localization\Profile;
use Symfony\Contracts\EventDispatcher\Event;

final class ProfileChangedEvent extends Event
{
	private Profile $profile;

	/**
	 * @param \App\Application\Localization\Profile $profile
	 */
	public function __construct(Profile $profile)
	{
		$this->profile = $profile;
	}

	/**
	 * @return \App\Application\Localization\Profile
	 */
	public function profile(): Profile
	{
		return $this->profile;
	}
}
