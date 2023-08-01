<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\CategoryList;

use App\Application\Acl\CategoryResource;
use App\Application\GlobalSettings\Locale;
use App\Domain\Category\Command\DeleteCategoryCommand;
use App\Domain\Category\Exception\CategoryNotFoundException;
use App\Domain\Category\ValueObject\CategoryId;
use App\ReadModel\Category\CategoriesDataGridQuery;
use App\ReadModel\Category\CategoryView;
use App\ReadModel\Category\GetCategoryByIdQuery;
use App\Web\Ui\Control;
use App\Web\Ui\DataGrid\DataGrid;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\Web\Ui\Modal\Confirm\ConfirmModalControl;
use App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface;
use Nette\Application\UI\Multiplier;
use Nette\InvalidStateException;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use Ublaboo\DataGrid\Exception\DataGridException;

final class CategoryListControl extends Control
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly DataGridFactoryInterface $dataGridFactory,
        private readonly ConfirmModalControlFactoryInterface $confirmModalControlFactory,
        private readonly ?Locale $locale,
    ) {}

    /**
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        $grid = $this->dataGridFactory->create(CategoriesDataGridQuery::create($this->locale?->code()));

        $grid->setTranslator($this->getPrefixedTranslator());
        $grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
        $grid->setTemplateVariables([
            '_locale' => $this->locale,
        ]);

        $grid->setDefaultSort([
            'created_at' => 'DESC',
        ]);

        $grid->addColumnText('name', 'name')
            ->setSortable('name')
            ->setFilterText('name');

        $grid->addColumnText('code', 'code', 'code.value')
            ->setSortable('code')
            ->setFilterText('code');

        $grid->addColumnText('active', 'active')
            ->setAlign('center')
            ->setFilterSelect(FilterHelper::bool($grid->getTranslator()));

        $grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
            ->setFormat('j.n.Y H:i:s')
            ->setSortable('createdAt')
            ->setFilterDate('createdAt');

        $grid->addAction('edit', '')
            ->setTemplate(__DIR__ . '/templates/action.edit.latte');

        $grid->addAction('delete', '')
            ->setTemplate(__DIR__ . '/templates/action.delete.latte');

        return $grid;
    }

    protected function createComponentDeleteConfirm(): Multiplier
    {
        if (!$this->getUser()->isAllowed(CategoryResource::class, CategoryResource::DELETE)) {
            throw new InvalidStateException('The user is not allowed to delete categories.');
        }

        return new Multiplier(function (string $id): ConfirmModalControl {
            $categoryId = CategoryId::fromString($id);
            $categoryView = $this->queryBus->dispatch(GetCategoryByIdQuery::create($categoryId->toString()));

            if (!$categoryView instanceof CategoryView) {
                throw new InvalidStateException('Category not found.');
            }

            $name = null !== $this->locale && isset($categoryView->names[$this->locale->code()]) ? $categoryView->names[$this->locale->code()]->value() : '?';
            $code = $categoryView->code->value();

            return $this->confirmModalControlFactory->create(
                '',
                $this->getPrefixedTranslator()->translate('delete_confirm.question', ['name' => $name, 'code' => $code]),
                function () use ($categoryView) {
                    try {
                        $this->commandBus->dispatch(DeleteCategoryCommand::create($categoryView->id->toString()));
                    } catch (CategoryNotFoundException $e) {
                    }

                    $this->getComponent('grid')->reload();
                    $this->closeModal();
                },
            );
        });
    }
}
