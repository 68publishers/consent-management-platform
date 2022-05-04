<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Filter;

use DateTimeZone;
use DateTimeImmutable;
use Nette\Forms\Container;
use App\Web\Ui\Form\Control\Flatpickr\Flatpickr;
use Ublaboo\DataGrid\Filter\FilterDate as UblabooFilterDate;

final class FilterDate extends UblabooFilterDate implements ConvertibleTimezoneDateFilterInterface
{
	use ConvertibleTimezoneDateFilterTrait;

	/** @var array  */
	protected $format = ['j. n. Y', 'j. n. Y'];

	/**
	 * @param \Nette\Forms\Container $container
	 *
	 * @return void
	 */
	public function addToFormContainer(Container $container): void
	{
		$container[$this->key] = $control  = Flatpickr::create($this->name)
			->setDateFormat($this->getJsFormat())
			->setReturnRealValue(FALSE);

		$this->addAttributes($control);

		if ($this->grid->hasAutoSubmit()) {
			$control->setHtmlAttribute('data-autosubmit-change', TRUE);
		}

		if ($this->getPlaceholder() !== NULL) {
			$control->setHtmlAttribute('placeholder', $this->getPlaceholder());
		}
	}

	/**
	 * @return array
	 */
	public function getCondition(): array
	{
		$condition = parent::getCondition();

		foreach ($condition as $column => $value) {
			$condition[$column] = DateTimeImmutable::createFromFormat($this->getPhpFormat(), $value, new DateTimeZone('UTC'));
		}

		return $condition;
	}
}
