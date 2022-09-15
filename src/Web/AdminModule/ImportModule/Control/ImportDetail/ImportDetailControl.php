<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\Web\Ui\Control;
use App\ReadModel\Import\ImportView;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByIdQuery;

final class ImportDetailControl extends Control
{
	private ImportView $importView;

	private QueryBusInterface $queryBus;

	/**
	 * @param \App\ReadModel\Import\ImportView                               $importView
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface $queryBus
	 */
	public function __construct(ImportView $importView, QueryBusInterface $queryBus)
	{
		$this->importView = $importView;
		$this->queryBus = $queryBus;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function beforeRender(): void
	{
		parent::beforeRender();

		$this->template->importView = $this->importView;
		$this->template->author = NULL !== $this->importView->authorId ? $this->queryBus->dispatch(GetUserByIdQuery::create($this->importView->authorId->toString())) : NULL;
	}
}
