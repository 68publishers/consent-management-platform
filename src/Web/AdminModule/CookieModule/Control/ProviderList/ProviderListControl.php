<?php

declare(strict_types=1);

namespace App\Web\AdminModule\CookieModule\Control\ProviderList;

use App\Application\Acl\CookieProviderResource;
use App\Domain\CookieProvider\Command\DeleteCookieProviderCommand;
use App\Domain\CookieProvider\Exception\CookieProviderNotFoundException;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\ReadModel\CookieProvider\CookieProvidersDataGridQuery;
use App\ReadModel\CookieProvider\CookieProviderView;
use App\ReadModel\CookieProvider\GetCookieProviderByIdQuery;
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

final class ProviderListControl extends Control
{
    private CommandBusInterface $commandBus;

    private QueryBusInterface $queryBus;

    private DataGridFactoryInterface $dataGridFactory;

    private ConfirmModalControlFactoryInterface $confirmModalControlFactory;

    public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus, DataGridFactoryInterface $dataGridFactory, ConfirmModalControlFactoryInterface $confirmModalControlFactory)
    {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
        $this->dataGridFactory = $dataGridFactory;
        $this->confirmModalControlFactory = $confirmModalControlFactory;
    }

    /**
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        $grid = $this->dataGridFactory->create(CookieProvidersDataGridQuery::create());

        $grid->setTranslator($this->getPrefixedTranslator());
        $grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

        $grid->setDefaultSort([
            'created_at' => 'DESC',
        ]);

        $grid->addColumnText('name', 'name', 'name.value')
            ->setSortable('name')
            ->setFilterText('name');

        $grid->addColumnText('code', 'code', 'code.value')
            ->setSortable('code')
            ->setFilterText('code');

        $grid->addColumnText('active', 'active')
            ->setAlign('center')
            ->setFilterSelect(FilterHelper::bool($grid->getTranslator()));

        $grid->addColumnText('private', 'private')
            ->setAlign('center')
            ->setFilterSelect(FilterHelper::bool($grid->getTranslator()));

        $grid->addColumnText('type', 'type', 'type.value')
            ->setFilterSelect(FilterHelper::select(ProviderType::values(), false, $grid->getTranslator(), '//layout.cookie_provider_type.'), 'type');

        $grid->addColumnText('link', 'link', 'link.value')
            ->setFilterText('link');

        $grid->addColumnNumber('number_of_cookies', 'number_of_cookies')
            ->setAlign('center')
            ->setSortable('numberOfCookies');

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
        if (!$this->getUser()->isAllowed(CookieProviderResource::class, CookieProviderResource::DELETE)) {
            throw new InvalidStateException('The user is not allowed to delete cookie providers.');
        }

        return new Multiplier(function (string $id): ConfirmModalControl {
            $cookieProviderId = CookieProviderId::fromString($id);
            $cookieProviderView = $this->queryBus->dispatch(GetCookieProviderByIdQuery::create($cookieProviderId->toString()));

            if (!$cookieProviderView instanceof CookieProviderView) {
                throw new InvalidStateException('Cookie provider not found.');
            }

            if ($cookieProviderView->private) {
                throw new InvalidStateException('Cookie provider is private and can not be deleted.');
            }

            $name = $cookieProviderView->name->value();

            return $this->confirmModalControlFactory->create(
                '',
                $this->getPrefixedTranslator()->translate('delete_confirm.question', ['name' => $name]),
                function () use ($cookieProviderView) {
                    try {
                        $this->commandBus->dispatch(DeleteCookieProviderCommand::create($cookieProviderView->id->toString()));
                    } catch (CookieProviderNotFoundException $e) {
                    }

                    $this->getComponent('grid')->reload();
                    $this->closeModal();
                },
            );
        });
    }
}
