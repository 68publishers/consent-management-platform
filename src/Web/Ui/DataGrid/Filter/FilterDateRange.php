<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Filter;

use DateTimeZone;
use DateTimeImmutable;
use Nette\Forms\Container;
use App\Web\Ui\Form\Control\Flatpickr\Flatpickr;
use Ublaboo\DataGrid\Filter\FilterDateRange as UblabooFilterDateRange;

final class FilterDateRange extends UblabooFilterDateRange implements ConvertibleTimezoneDateFilterInterface
{
	use ConvertibleTimezoneDateFilterTrait;

	/** @var array  */
	protected $format = ['j. n. Y', 'j. n. Y'];

	/**
	 * {@inheritDoc}
	 */
	public function addToFormContainer(Container $container): void
	{
		$container = $container->addContainer($this->key);

		$container['from'] = $from  = Flatpickr::create($this->name)
			->setDateFormat($this->getJsFormat())
			->setReturnRealValue(FALSE);

		$container['to'] = $to = Flatpickr::create($this->nameSecond)
			->setDateFormat($this->getJsFormat())
			->setReturnRealValue(FALSE);

		$this->addAttributes($from);
		$this->addAttributes($to);

		if ($this->grid->hasAutoSubmit()) {
			$from->setHtmlAttribute('data-autosubmit-change', TRUE);
			$to->setHtmlAttribute('data-autosubmit-change', TRUE);
		}

		$placeholders = $this->getPlaceholders();

		if ($placeholders !== []) {
			$textFrom = reset($placeholders);

			if ($textFrom) {
				$from->setHtmlAttribute('placeholder', $textFrom);
			}

			$textTo = end($placeholders);

			if ($textTo && ($textTo !== $textFrom)) {
				$to->setHtmlAttribute('placeholder', $textTo);
			}
		}
	}

	/**
	 * @return array
	 */
	public function getCondition(): array
	{
		$condition = parent::getCondition();

		foreach ($condition as $column => $values) {
			foreach ($values as $key => $value) {
				if (!empty($value)) {
					$values[$key] = DateTimeImmutable::createFromFormat($this->getPhpFormat(), $value, new DateTimeZone('UTC'));
				}
			}

			$condition[$column] = $values;
		}

		return $condition;
	}
}
