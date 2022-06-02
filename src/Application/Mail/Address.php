<?php

declare(strict_types=1);

namespace App\Application\Mail;

final class Address
{
	private string $from;

	private ?string $name;

	private function __construct()
	{
	}

	/**
	 * @param string      $from
	 * @param string|NULL $name
	 *
	 * @return static
	 */
	public static function create(string $from, ?string $name = NULL): self
	{
		$address = new self();
		$address->from = $from;
		$address->name = empty($name) ? NULL : $name;

		return $address;
	}

	/**
	 * @return string
	 */
	public function from(): string
	{
		return $this->from;
	}

	/**
	 * @return string|NULL
	 */
	public function name(): ?string
	{
		return $this->name;
	}
}
