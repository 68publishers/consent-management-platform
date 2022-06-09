<?php

declare(strict_types=1);

namespace App\Web\Control\Localization\Event;

use Throwable;
use Symfony\Contracts\EventDispatcher\Event;

final class ProfileChangeFailed extends Event
{
	private Throwable $error;

	private string $profileCode;

	/**
	 * @param \Throwable $error
	 * @param string     $profileCode
	 */
	public function __construct(Throwable $error, string $profileCode)
	{
		$this->error = $error;
		$this->profileCode = $profileCode;
	}

	/**
	 * @return \Throwable
	 */
	public function error(): Throwable
	{
		return $this->error;
	}

	/**
	 * @return string
	 */
	public function profileCode(): string
	{
		return $this->profileCode;
	}
}
