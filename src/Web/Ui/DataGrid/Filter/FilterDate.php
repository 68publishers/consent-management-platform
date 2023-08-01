<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid\Filter;

use App\Web\Ui\Form\Control\Flatpickr\Flatpickr;
use DateTimeImmutable;
use DateTimeZone;
use Nette\Forms\Container;
use Ublaboo\DataGrid\Filter\FilterDate as UblabooFilterDate;

final class FilterDate extends UblabooFilterDate implements ConvertibleTimezoneDateFilterInterface
{
    use ConvertibleTimezoneDateFilterTrait;

    /** @var array  */
    protected $format = ['j. n. Y', 'j. n. Y'];

    public function addToFormContainer(Container $container): void
    {
        $container[$this->key] = $control  = Flatpickr::create($this->name)
            ->setDateFormat($this->getJsFormat())
            ->setReturnRealValue(false);

        $this->addAttributes($control);

        if ($this->grid->hasAutoSubmit()) {
            $control->setHtmlAttribute('data-autosubmit-change');
        }

        if ($this->getPlaceholder() !== null) {
            $control->setHtmlAttribute('placeholder', $this->getPlaceholder());
        }
    }

    public function getCondition(): array
    {
        $condition = parent::getCondition();

        foreach ($condition as $column => $value) {
            $condition[$column] = DateTimeImmutable::createFromFormat($this->getPhpFormat(), $value, new DateTimeZone($this->getTimezoneTo()));
        }

        return $condition;
    }
}
