<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserList;

use App\Application\Acl\UserResource;
use App\Domain\User\RolesEnum;
use App\ReadModel\User\UsersDataGridQuery;
use App\ReadModel\User\UserView;
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
use SixtyEightPublishers\UserBundle\Domain\Command\DeleteUserCommand;
use SixtyEightPublishers\UserBundle\Domain\Exception\UserNotFoundException;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByIdQuery;
use Ublaboo\DataGrid\Exception\DataGridException;

final class UserListControl extends Control
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly DataGridFactoryInterface $dataGridFactory,
        private readonly ConfirmModalControlFactoryInterface $confirmModalControlFactory,
    ) {}

    /**
     * @throws DataGridException
     */
    protected function createComponentGrid(): DataGrid
    {
        $grid = $this->dataGridFactory->create(UsersDataGridQuery::create());

        $grid->setTranslator($this->getPrefixedTranslator());
        $grid->setTemplateFile(__DIR__ . '/templates/datagrid.latte');

        $grid->setDefaultSort([
            'created_at' => 'DESC',
        ]);

        $grid->addColumnText('id', 'id', 'id.toString')
            ->setFilterText('id');

        $grid->addColumnText('email_address', 'email_address', 'emailAddress.value')
            ->setSortable('emailAddress')
            ->setFilterText('emailAddress');

        $grid->addColumnText('name', 'name', 'name.name')
            ->setSortable('name')
            ->setFilterText('name');

        $grid->addColumnDateTimeTz('created_at', 'created_at', 'createdAt')
            ->setFormat('j.n.Y H:i:s')
            ->setSortable('createdAt')
            ->setFilterDate('createdAt');

        $grid->addColumnText('roles', 'roles')
            ->setFilterMultiSelect(FilterHelper::items(RolesEnum::values(), false, $this->getTranslator(), '//layout.role_name.'));

        $grid->addAction('edit', '')
            ->setTemplate(__DIR__ . '/templates/action.edit.latte');

        $grid->addAction('delete', '')
            ->setTemplate(__DIR__ . '/templates/action.delete.latte');

        return $grid;
    }

    protected function createComponentDeleteConfirm(): Multiplier
    {
        if (!$this->getUser()->isAllowed(UserResource::class, UserResource::DELETE)) {
            throw new InvalidStateException('The user is not allowed to delete users.');
        }

        return new Multiplier(function (string $id): ConfirmModalControl {
            $userId = UserId::fromString($id);
            $userView = $this->queryBus->dispatch(GetUserByIdQuery::create($userId->toString()));

            if (!$userView instanceof UserView) {
                throw new InvalidStateException('User not found.');
            }

            $name = $userView->name->name();

            return $this->confirmModalControlFactory->create(
                '',
                $this->getPrefixedTranslator()->translate('delete_confirm.question', ['name' => $name]),
                function () use ($userView) {
                    try {
                        $this->commandBus->dispatch(DeleteUserCommand::create($userView->id->toString()));
                    } catch (UserNotFoundException $e) {
                    }

                    $this->getComponent('grid')->reload();
                    $this->closeModal();
                },
            );
        });
    }
}
