<?php

declare(strict_types=1);

namespace App\Web\Ui\Form\Control\Flatpickr;

use DateTime;
use Throwable;
use DateTimeZone;
use Nette\Utils\Html;
use DateTimeInterface;
use Nette\Utils\Strings;
use Nette\Utils\Validators;
use Nette\Forms\Controls\TextInput;
use Nette\Utils\AssertionException;

final class Flatpickr extends TextInput
{
	public const ATTRIBUTE_MODE = 'data-mode';
	public const ATTRIBUTE_NO_CALENDAR = 'data-no-calendar';
	public const ATTRIBUTE_ENABLE_TIME = 'data-enable-time';
	public const ATTRIBUTE_DATE_FORMAT = 'data-date-format';
	public const ATTRIBUTE_TIME_24_HR = 'data-time_24hr';
	public const ATTRIBUTE_ALLOW_INPUT = 'data-allow-input';
	public const ATTRIBUTE_DEFAULT_HOUR = 'data-default-hour';
	public const ATTRIBUTE_DEFAULT_MINUTE = 'data-default-minute';

	public const MODE_SINGLE = 'single';
	public const MODE_MULTIPLE = 'multiple';
	public const MODE_RANGE = 'range';
	public const MODE_DEFAULT = self::MODE_SINGLE;

	private array $attributes = [
		'x-data' => '',
		'x-flatpickr' => '',
		self::ATTRIBUTE_MODE => self::MODE_DEFAULT,
		self::ATTRIBUTE_NO_CALENDAR => FALSE,
		self::ATTRIBUTE_ENABLE_TIME => FALSE,
		self::ATTRIBUTE_DATE_FORMAT => 'j.n.Y',
		self::ATTRIBUTE_TIME_24_HR => FALSE,
		self::ATTRIBUTE_ALLOW_INPUT => FALSE,
		self::ATTRIBUTE_DEFAULT_HOUR => NULL,
		self::ATTRIBUTE_DEFAULT_MINUTE => NULL,
	];

	/** @var NULL|\DateTimeInterface|\DateTimeInterface[] */
	private $realValue;

	private bool $returnRealValue = TRUE;

	private ?DateTimeZone $dateTimeZone = NULL;

	/**
	 * @param NULL|string $label
	 */
	public function __construct(?string $label = NULL)
	{
		parent::__construct($label);

		$this->setNullable(TRUE);
	}

	/**
	 * @param string|NULL $label
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public static function create(?string $label = NULL): self
	{
		return new self($label);
	}

	/**
	 * {@inheritdoc}
	 *
	 * @throws \Throwable
	 */
	public function setValue($value)
	{
		try {
			if (!empty($value)) {
				if (is_string($value)) {
					$this->realValue = $this->rawToReal($value);
				} else {
					$this->realValue = $value;
					$value = $this->realToRaw($value);
				}
			} else {
				$this->realValue = NULL;
			}

			/** @noinspection PhpInternalEntityUsedInspection */
			parent::setValue($value);
		} catch (FlatpickrException $e) {
			if (FALSE === $this->attributes[self::ATTRIBUTE_ALLOW_INPUT]) {
				throw $e;
			}
		}

		return $this;
	}

	/**
	 * @return \DateTimeInterface|\DateTimeInterface[]|NULL|string
	 */
	public function getValue()
	{
		return TRUE === $this->returnRealValue
			? $this->realValue
			: parent::getValue();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getControl(): Html
	{
		return parent::getControl()
			->addAttributes(array_map(
				static fn ($value) => is_bool($value) ? ($value ? 'true' : 'false') : $value,
				array_filter($this->attributes, static fn ($value) => NULL !== $value)
			));
	}

	/**
	 * @param string $mode
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 * @throws \App\Web\Ui\Form\Control\Flatpickr\FlatpickrException
	 */
	public function setMode(string $mode): self
	{
		if (!in_array($mode, [self::MODE_SINGLE, self::MODE_MULTIPLE, self::MODE_RANGE], TRUE)) {
			throw $this->error(sprintf('Mode %s is not supported', $mode));
		}

		$this->attributes[self::ATTRIBUTE_MODE] = $mode;

		return $this;
	}

	/**
	 * @param string $dateFormat
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public function setDateFormat(string $dateFormat): self
	{
		$this->attributes[self::ATTRIBUTE_DATE_FORMAT] = $dateFormat;

		return $this;
	}

	/**
	 * @param bool $noCalendar
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public function setNoCalendar(bool $noCalendar = TRUE): self
	{
		$this->attributes[self::ATTRIBUTE_NO_CALENDAR] = $noCalendar;

		return $this;
	}

	/**
	 * @param bool $enableTime
	 * @param bool $time24hr
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public function setEnableTime(bool $enableTime = TRUE, bool $time24hr = TRUE): self
	{
		$this->attributes[self::ATTRIBUTE_ENABLE_TIME] = $enableTime;
		$this->attributes[self::ATTRIBUTE_TIME_24_HR] = $time24hr;

		return $this;
	}

	/**
	 * @param bool $allowInput
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public function setAllowInput(bool $allowInput = TRUE): self
	{
		$this->attributes[self::ATTRIBUTE_ALLOW_INPUT] = $allowInput;

		return $this;
	}

	/**
	 * @param int $hour
	 * @param int $minute
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public function setDefaultTime(int $hour, int $minute): self
	{
		$this->attributes[self::ATTRIBUTE_DEFAULT_HOUR] = $hour;
		$this->attributes[self::ATTRIBUTE_DEFAULT_MINUTE] = $minute;

		return $this;
	}

	/**
	 * @param bool $returnRealValue
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public function setReturnRealValue(bool $returnRealValue): self
	{
		$this->returnRealValue = $returnRealValue;

		return $this;
	}

	/**
	 * @param \DateTimeZone $dateTimeZone
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 */
	public function setDateTimeZone(DateTimeZone $dateTimeZone): self
	{
		$this->dateTimeZone = $dateTimeZone;

		return $this;
	}

	/**
	 * @param string $rawValue
	 *
	 * @return \DateTimeInterface|\DateTimeInterface[]
	 * @throws \Throwable
	 */
	private function rawToReal(string $rawValue)
	{
		switch ($this->attributes[self::ATTRIBUTE_MODE]) {
			case self::MODE_SINGLE:

				return $this->createDateTimeFromString($rawValue);
			case self::MODE_MULTIPLE:

				return array_map(function (string $dateTime) {
					return $this->createDateTimeFromString($dateTime);
				}, explode(',', $rawValue));
			case self::MODE_RANGE:
				$values = array_map(function (string $dateTime) {
					return $this->createDateTimeFromString($dateTime);
				}, explode('to', $rawValue));

				if (2 !== count($values)) {
					throw $this->error('Passed value must be in format "[date] to [date]" if mode is set to "range"');
				}

				return [
					'from' => array_shift($values),
					'to' => array_shift($values),
				];
		}

		throw $this->error(sprintf(
			'Invalid mode %s',
			$this->attributes[self::ATTRIBUTE_MODE]
		));
	}

	/**
	 * @param mixed $realValue
	 *
	 * @return string
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 * @throws \App\Web\Ui\Form\Control\Flatpickr\FlatpickrException
	 */
	private function realToRaw($realValue): string
	{
		$this->assertRealValue($realValue);
		switch ($this->attributes[self::ATTRIBUTE_MODE]) {
			case self::MODE_SINGLE:

				return $realValue->setTimezone($this->getDateTimeZone())->format($this->attributes[self::ATTRIBUTE_DATE_FORMAT]);
			case self::MODE_MULTIPLE:

				return implode(', ', array_map(function (DateTimeInterface $dateTime) {
					return $dateTime->setTimezone($this->getDateTimeZone())->format($this->attributes[self::ATTRIBUTE_DATE_FORMAT]);
				}, $realValue));
			case self::MODE_RANGE:

				return sprintf(
					'%s to %s',
					$realValue['from']->setTimezone($this->getDateTimeZone())->format($this->attributes[self::ATTRIBUTE_DATE_FORMAT]),
					$realValue['to']->setTimezone($this->getDateTimeZone())->format($this->attributes[self::ATTRIBUTE_DATE_FORMAT])
				);
		}

		throw $this->error(sprintf(
			'Invalid mode %s',
			$this->attributes[self::ATTRIBUTE_MODE]
		));
	}

	/**
	 * @param string $dateTime
	 *
	 * @return \DateTimeInterface
	 * @return \App\Web\Ui\Form\Control\Flatpickr\Flatpickr
	 * @throws \Throwable
	 */
	private function createDateTimeFromString(string $dateTime): DateTimeInterface
	{
		try {
			$dateTime = DateTime::createFromFormat(
				$this->attributes[self::ATTRIBUTE_DATE_FORMAT],
				Strings::trim($dateTime),
				$this->getDateTimeZone()
			);

			if (FALSE === $dateTime) {
				throw $this->error('Invalid Datetime string representation value.');
			}

			if (FALSE === $this->attributes[self::ATTRIBUTE_ENABLE_TIME]) {
				$dateTime->setTime(0, 0, 0, 0);
			}

			return $dateTime;
		} catch (Throwable $e) {
			if (!$e instanceof FlatpickrException) {
				$e = $this->error($e->getMessage());
			}

			throw $e;
		}
	}

	/**
	 * @param string $message
	 *
	 * @return \App\Web\Ui\Form\Control\Flatpickr\FlatpickrException
	 */
	private function error(string $message): FlatpickrException
	{
		return new FlatpickrException($message);
	}

	/**
	 * @param $realValue
	 *
	 * @return void
	 * @throws \App\Web\Ui\Form\Control\Flatpickr\FlatpickrException
	 */
	private function assertRealValue($realValue): void
	{
		try {
			switch ($this->attributes[self::ATTRIBUTE_MODE]) {
				case self::MODE_SINGLE:
					Validators::assert($realValue, DateTimeInterface::class);

					break;
				case self::MODE_MULTIPLE:
					Validators::assert($realValue, DateTimeInterface::class . '[]');

					break;
				case self::MODE_RANGE:
					Validators::assertField($realValue, 'from', DateTimeInterface::class);
					Validators::assertField($realValue, 'to', DateTimeInterface::class);

					break;
			}
		} catch (AssertionException $e) {
			throw $this->error(sprintf(
				'Invalid value passed to method %s. Original message: %s',
				__METHOD__,
				$e->getMessage()
			));
		}
	}

	/**
	 * @return \DateTimeZone
	 */
	private function getDateTimeZone(): DateTimeZone
	{
		if (NULL === $this->dateTimeZone) {
			$this->setDateTimeZone(new DateTimeZone('UTC'));
		}

		return $this->dateTimeZone;
	}
}
