<?php

declare(strict_types=1);

namespace App\Web\Control\Localization;

use App\Web\Ui\Control;
use InvalidArgumentException;
use App\Application\Localization\Profiles;
use App\Web\Control\Localization\Event\ProfileChangedEvent;
use App\Web\Control\Localization\Event\ProfileChangeFailed;

final class LocalizationControl extends Control
{
	private Profiles $profiles;

	/**
	 * @param \App\Application\Localization\Profiles $profiles
	 */
	public function __construct(Profiles $profiles)
	{
		$this->profiles = $profiles;
	}

	/**
	 * @param string $code
	 *
	 * @return void
	 */
	public function handleChange(string $code): void
	{
		try {
			$this->dispatchEvent(new ProfileChangedEvent($this->profiles->get($code)));
		} catch (InvalidArgumentException $e) {
			$this->dispatchEvent(new ProfileChangeFailed($e, $code));
		}
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->profiles = $this->profiles;
	}
}
