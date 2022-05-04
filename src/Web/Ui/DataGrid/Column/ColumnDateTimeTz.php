<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Column;

use DateTime;
use DateTimeZone;
use Ublaboo\DataGrid\Row;
use Ublaboo\DataGrid\Column\Column;
use Ublaboo\DataGrid\Utils\DateTimeHelper;
use Ublaboo\DataGrid\Exception\DataGridDateTimeHelperException;

final class ColumnDateTimeTz extends Column
{
	/** @var string  */
	protected $align = 'right';

	private string $format = 'j. n. Y';

	private ?DateTimeZone $timezone = NULL;

	/**
	 * {@inheritDoc}
	 */
	public function getColumnValue(Row $row)
	{
		$value = parent::getColumnValue($row);

		if (!$value instanceof DateTime) {
			try {
				$value = DateTimeHelper::tryConvertToDateTime($value);
			} catch (DataGridDateTimeHelperException $e) {
				return $value;
			}
		}

		$value->setTimezone($this->getTimezone());

		return $value->format($this->format);
	}

	/**
	 * @param string $timezone
	 *
	 * @return $this
	 */
	public function setTimezone(string $timezone): self
	{
		$this->timezone = new DateTimeZone($timezone);

		return $this;
	}

	/**
	 * @return \DateTimeZone
	 */
	public function getTimezone(): DateTimeZone
	{
		if (NULL === $this->timezone) {
			$this->setTimezone('UTC');
		}

		return $this->timezone;
	}

	/**
	 * @param string $format
	 *
	 * @return \App\Web\Ui\DataGrid\Column\ColumnDateTimeTz
	 */
	public function setFormat(string $format): self
	{
		$this->format = $format;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFormat(): string
	{
		return $this->format;
	}
}
