<?php

declare(strict_types=1);

namespace App\Web\Ui\DataGrid;

use Nette\Localization\ITranslator;
use Ublaboo\DataGrid\Column\Column;
use Ublaboo\DataGrid\Column\ColumnNumber;
use App\Web\Ui\DataGrid\Filter\FilterDate;
use App\Web\Ui\DataGrid\Filter\FilterDateRange;
use App\Web\Ui\DataGrid\Column\ColumnDateTimeTz;
use Ublaboo\DataGrid\DataGrid as UblabooDataGrid;
use App\Web\Ui\DataGrid\Translator\TranslatorProxy;
use App\Application\Localization\ApplicationDateTimeZone;
use Ublaboo\DataGrid\Filter\FilterDate as UblabooFilterDate;
use Ublaboo\DataGrid\Filter\FilterDateRange as UblabooFilterDateRange;

class DataGrid extends UblabooDataGrid
{
	private ?string $sessionNamePostfix = NULL;

	private array $templateVariables = [];

	/**
	 * @param \Nette\Localization\ITranslator|\Nette\Localization\Translator $translator
	 *
	 * @return \Ublaboo\DataGrid\DataGrid
	 */
	public function setTranslator(ITranslator $translator): UblabooDataGrid
	{
		/** @noinspection PhpParamsInspection */
		return parent::setTranslator(new TranslatorProxy($translator));
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
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

	/**
	 * {@inheritdoc}
	 */
	protected function addColumn($key, Column $column): Column
	{
		return parent::addColumn($key, $column->setAlign(
			$column instanceof ColumnNumber ? 'center' : 'left'
		));
	}

	/**
	 * @param string      $key
	 * @param string      $name
	 * @param string|NULL $column
	 * @param string|NULL $timezone
	 *
	 * @return \App\Web\Ui\DataGrid\Column\ColumnDateTimeTz
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function addColumnDateTimeTz(string $key, string $name, ?string $column = NULL, ?string $timezone = NULL): ColumnDateTimeTz
	{
		$column = $column ?: $key;
		$columnDateTimeTz = new ColumnDateTimeTz($this, $key, $column, $name);
		$timezone = $timezone ?? ApplicationDateTimeZone::get()->getName();

		$columnDateTimeTz->setTimezone($timezone);
		$this->addColumn($key, $columnDateTimeTz);

		return $columnDateTimeTz;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @return \App\Web\Ui\DataGrid\Filter\FilterDate
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function addFilterDate(string $key, string $name, ?string $column = NULL): UblabooFilterDate
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
	 * {@inheritDoc}
	 *
	 * @return \App\Web\Ui\DataGrid\Filter\FilterDateRange
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function addFilterDateRange(string $key, string $name, ?string $column = NULL, string $nameSecond = '-'): UblabooFilterDateRange
	{
		$column = $column ?? $key;

		$this->addFilterCheck($key);

		$filter = $this->filters[$key] = new FilterDateRange($this, $key, $name, $column, $nameSecond);

		if (isset($this->columns[$key]) && ($col = $this->columns[$key]) instanceof ColumnDateTimeTz) {
			$filter->setTimezoneTo($col->getTimezone()->getName());
		}

		return $filter;
	}

	/**
	 * @param string|NULL $sessionNamePostfix
	 */
	public function setSessionNamePostfix(?string $sessionNamePostfix): void
	{
		$this->sessionNamePostfix = $sessionNamePostfix;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSessionSectionName(): string
	{
		$name = parent::getSessionSectionName();

		if (NULL !== $this->sessionNamePostfix) {
			$name .= ':' . $this->sessionNamePostfix;
		}

		return $name;
	}

	/**
	 * @return string
	 */
	public function getOriginalTemplateFile(): string
	{
		return __DIR__ . '/../templates/datagrid/datagrid.latte';
	}

	/**
	 * @param array $templateVariables
	 *
	 * @return $this
	 */
	public function setTemplateVariables(array $templateVariables): self
	{
		$this->templateVariables = $templateVariables;

		return $this;
	}

	/**
	 * @param string $name
	 * @param mixed  $value
	 *
	 * @return $this
	 */
	public function addTemplateVariable(string $name, $value): self
	{
		$this->templateVariables[$name] = $value;

		return $this;
	}
}
