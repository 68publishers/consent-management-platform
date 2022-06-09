<?php

declare(strict_types=1);

namespace App\ReadModel\User;

use DateTimeZone;
use App\Domain\Shared\ValueObject\Locale;
use SixtyEightPublishers\UserBundle\ReadModel\View\UserView as BaseUserView;

final class UserView extends BaseUserView
{
	public Locale $profileLocale;

	public DateTimeZone $timezone;

	/**
	 * {@inheritDoc}
	 */
	public function jsonSerialize(): array
	{
		$data = parent::jsonSerialize();
		$data['profile'] = $this->profileLocale->value();
		$data['timezone'] = $this->timezone->getName();

		return $data;
	}
}
