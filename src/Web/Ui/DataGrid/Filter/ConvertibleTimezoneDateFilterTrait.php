<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Filter;

trait ConvertibleTimezoneDateFilterTrait
{
	private string $timezoneFrom = 'UTC';

	private string $timezoneTo = 'UTC';

	/**
	 * @param string $timezone
	 *
	 * @return self
	 */
	public function setTimezoneFrom(string $timezone): self
	{
		$this->timezoneFrom = $timezone;

		return $this;
	}

	/**
	 * @param string $timezone
	 *
	 * @return self
	 */
	public function setTimezoneTo(string $timezone): self
	{
		$this->timezoneTo = $timezone;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTimezoneFrom(): string
	{
		return $this->timezoneFrom;
	}

	/**
	 * @return string
	 */
	public function getTimezoneTo(): string
	{
		return $this->timezoneTo;
	}
}
