<?php

declare(strict_types=1);

namespace App\Application;

final class OpenApiConfiguration
{
	private bool $enabled;

	private string $endpoint;

	/**
	 * @param bool   $enabled
	 * @param string $endpoint
	 */
	public function __construct(bool $enabled, string $endpoint)
	{
		$this->enabled = $enabled;
		$this->endpoint = $endpoint;
	}

	/**
	 * @return bool
	 */
	public function enabled(): bool
	{
		return $this->enabled;
	}

	/**
	 * @return string
	 */
	public function endpoint(): string
	{
		return $this->endpoint;
	}
}
