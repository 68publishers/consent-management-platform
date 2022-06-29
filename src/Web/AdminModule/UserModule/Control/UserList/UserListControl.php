<?php

declare(strict_types=1);

namespace App\Web\AdminModule\UserModule\Control\UserList;

use App\Web\Ui\Control;
use App\Domain\User\RolesEnum;
use App\ReadModel\User\UserView;
use Nette\InvalidStateException;
use App\Web\Ui\DataGrid\DataGrid;
use Nette\Application\UI\Multiplier;
use App\Application\Acl\UserResource;
use App\ReadModel\User\UsersDataGridQuery;
use App\Web\Ui\DataGrid\Helper\FilterHelper;
use App\Web\Ui\DataGrid\DataGridFactoryInterface;
use App\Web\Ui\Modal\Confirm\ConfirmModalControl;
use SixtyEightPublishers\UserBundle\Domain\ValueObject\UserId;
use App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\DeleteUserCommand;
use SixtyEightPublishers\UserBundle\ReadModel\Query\GetUserByIdQuery;
use SixtyEightPublishers\UserBundle\Domain\Exception\UserNotFoundException;

final class UserListControl extends Control
{
	private CommandBusInterface $commandBus;

	private QueryBusInterface $queryBus;

	private DataGridFactoryInterface $dataGridFactory;

	private ConfirmModalControlFactoryInterface $confirmModalControlFactory;

	/**
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface $commandBus
	 * @param \SixtyEightPublishers\ArchitectureBundle\Bus\QueryBusInterface   $queryBus
	 * @param \App\Web\Ui\DataGrid\DataGridFactoryInterface                    $dataGridFactory
	 * @param \App\Web\Ui\Modal\Confirm\ConfirmModalControlFactoryInterface    $confirmModalControlFactory
	 */
	public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus, DataGridFactoryInterface $dataGridFactory, ConfirmModalControlFactoryInterface $confirmModalControlFactory)
	{
		$this->commandBus = $commandBus;
		$this->queryBus = $queryBus;
		$this->dataGridFactory = $dataGridFactory;
		$this->confirmModalControlFactory = $confirmModalControlFactory;
	}

	/**
	 * @return \App\Web\Ui\DataGrid\DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
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
			->setFilterMultiSelect(FilterHelper::items(RolesEnum::values(), FALSE, $this->getTranslator(), '//layout.role_name.'));

		$grid->addAction('edit', '')
			->setTemplate(__DIR__ . '/templates/action.edit.latte');

		$grid->addAction('delete', '')
			->setTemplate(__DIR__ . '/templates/action.delete.latte');

		return $grid;
	}

	/**
	 * @return \Nette\Application\UI\Multiplier
	 */
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
				}
			);
		});
	}
}
