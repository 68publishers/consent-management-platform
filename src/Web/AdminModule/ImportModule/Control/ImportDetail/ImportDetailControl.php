<?php

declare(strict_types=1);

namespace App\Web\AdminModule\ImportModule\Control\ImportDetail;

use App\ReadModel\Import\ImportView;
use App\Web\Ui\Control;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByIdQuery;

final class ImportDetailControl extends Control
{
    private ImportView $importView;

    private QueryBusInterface $queryBus;

    public function __construct(ImportView $importView, QueryBusInterface $queryBus)
    {
        $this->importView = $importView;
        $this->queryBus = $queryBus;
    }

    protected function beforeRender(): void
    {
        parent::beforeRender();

        $template = $this->getTemplate();
        assert($template instanceof ImportDetailTemplate);

        $template->importView = $this->importView;
        $template->author = null !== $this->importView->authorId ? $this->queryBus->dispatch(GetUserByIdQuery::create($this->importView->authorId->toString())) : null;
    }
}
