<?php

declare(strict_types=1);

namespace App\Application\Cookie;

final class TemplateArguments
{
	private array $providers;

	private array $cookies;

	private function __construct()
	{
	}

	/**
	 * @param object[] $providers
	 * @param object[] $cookies
	 *
	 * @return $this
	 */
	public static function create(array $providers, array $cookies): self
	{
		$arguments = new self();
		$arguments->providers = $providers;
		$arguments->cookies = $cookies;

		return $arguments;
	}

	/**
	 * @return object[]
	 */
	public function providers(): array
	{
		return $this->providers;
	}

	/**
	 * @return object[]
	 */
	public function cookies(): array
	{
		return $this->cookies;
	}
}
