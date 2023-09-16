<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use App\Application\Localization\ApplicationDateTimeZone;
use App\Web\Ui\DataGrid\Column\ColumnDateTimeTz;
use App\Web\Ui\DataGrid\Filter\FilterDate;
use App\Web\Ui\DataGrid\Filter\FilterDateRange;
use App\Web\Ui\DataGrid\Translator\TranslatorProxy;
use Nette\Localization\ITranslator;
use Nette\Localization\Translator;
use Ublaboo\DataGrid\Column\Column;
use Ublaboo\DataGrid\Column\ColumnNumber;
use Ublaboo\DataGrid\DataGrid as UblabooDataGrid;
use Ublaboo\DataGrid\DataModel;
use Ublaboo\DataGrid\Exception\DataGridException;
use Ublaboo\DataGrid\Filter\FilterDate as UblabooFilterDate;
use Ublaboo\DataGrid\Filter\FilterDateRange as UblabooFilterDateRange;

class DataGrid extends UblabooDataGrid
{
    private ?string $sessionNamePostfix = null;

    private array $templateVariables = [];

    /**
     * @param ITranslator|Translator $translator
     */
    public function setTranslator(ITranslator $translator): UblabooDataGrid
    {
        /** @noinspection PhpParamsInspection */
        return parent::setTranslator(new TranslatorProxy($translator));
    }

    /**
     * @throws DataGridException
     */
    public function render(): void
    {
        $this->getTemplate()->ublabooTemplateFile = parent::getOriginalTemplateFile();
        $this->getTemplate()->linkFactory = new LinkFactory($this);

        foreach ($this->templateVariables as $name => $value) {
            $this->getTemplate()->{$name} = $value;
        }

        parent::render();
    }

    protected function addColumn($key, Column $column): Column
    {
        return parent::addColumn($key, $column->setAlign(
            $column instanceof ColumnNumber ? 'center' : 'left',
        ));
    }

    /**
     * @throws DataGridException
     */
    public function addColumnDateTimeTz(string $key, string $name, ?string $column = null, ?string $timezone = null): ColumnDateTimeTz
    {
        $column = $column ?: $key;
        $columnDateTimeTz = new ColumnDateTimeTz($this, $key, $column, $name);
        $timezone = $timezone ?? ApplicationDateTimeZone::get()->getName();

        $columnDateTimeTz->setTimezone($timezone);
        $this->addColumn($key, $columnDateTimeTz);

        return $columnDateTimeTz;
    }

    /**
     * @return FilterDate
     * @throws DataGridException
     */
    public function addFilterDate(string $key, string $name, ?string $column = null): UblabooFilterDate
    {
        $column = $column ?: $key;

        $this->addFilterCheck($key);

        $filter = $this->filters[$key] = new FilterDate($this, $key, $name, $column);

        if (isset($this->columns[$key]) && ($col = $this->columns[$key]) instanceof ColumnDateTimeTz) {
            $filter->setTimezoneTo($col->getTimezone()->getName());
        }

        return $filter;
    }

    /**
     * @return FilterDateRange
     * @throws DataGridException
     */
    public function addFilterDateRange(string $key, string $name, ?string $column = null, string $nameSecond = '-'): UblabooFilterDateRange
    {
        $column = $column ?? $key;

        $this->addFilterCheck($key);

        $filter = $this->filters[$key] = new FilterDateRange($this, $key, $name, $column, $nameSecond);

        if (isset($this->columns[$key]) && ($col = $this->columns[$key]) instanceof ColumnDateTimeTz) {
            $filter->setTimezoneTo($col->getTimezone()->getName());
        }

        return $filter;
    }

    public function setSessionNamePostfix(?string $sessionNamePostfix): void
    {
        $this->sessionNamePostfix = $sessionNamePostfix;
    }

    public function getSessionSectionName(): string
    {
        $name = parent::getSessionSectionName();

        if (null !== $this->sessionNamePostfix) {
            $name .= ':' . $this->sessionNamePostfix;
        }

        return $name;
    }

    public function getOriginalTemplateFile(): string
    {
        return __DIR__ . '/../templates/datagrid/datagrid.latte';
    }

    public function setTemplateVariables(array $templateVariables): self
    {
        $this->templateVariables = $templateVariables;

        return $this;
    }

    public function addTemplateVariable(string $name, mixed $value): self
    {
        $this->templateVariables[$name] = $value;

        return $this;
    }

    public function getDataModel(): ?DataModel
    {
        return $this->dataModel;
    }
}
