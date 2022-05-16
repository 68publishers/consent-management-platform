<?php

declare(strict_types=1);

namespace App\Web\Ui\Modal;

final class HtmlId
{
	private string $id;

	private function __construct()
	{
	}

	/**
	 * @param string $id
	 *
	 * @return static
	 */
	public static function create(string $id): self
	{
		$htmlId = new self();
		$htmlId->id = $id;

		return $htmlId;
	}

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return $this->id;
	}
}
