<?php

declare(strict_types=1);

namespace App\Web\Ui\Form;

final class RecaptchaResolver
{
	private bool $isEnabled;

	/**
	 * @param bool $isEnabled
	 */
	public function __construct(bool $isEnabled)
	{
		$this->isEnabled = $isEnabled;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return $this->isEnabled;
	}
}
