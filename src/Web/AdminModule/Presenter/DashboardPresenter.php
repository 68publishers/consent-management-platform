<?php

namespace App\Web\AdminModule\Presenter;

use App\Web\Ui\Presenter;
use SixtyEightPublishers\UserBundle\Domain\Dto\Role;
use SixtyEightPublishers\UserBundle\Domain\Dto\Roles;
use SixtyEightPublishers\UserBundle\Domain\Dto\UserId;
use SixtyEightPublishers\UserBundle\Domain\Dto\Username;
use SixtyEightPublishers\UserBundle\Domain\Dto\Password;
use SixtyEightPublishers\UserBundle\Domain\Dto\EmailAddress;
use SixtyEightPublishers\UserBundle\Domain\Command\CreateUserCommand;
use SixtyEightPublishers\ArchitectureBundle\Domain\Command\CommandBusInterface;

final class DashboardPresenter extends Presenter
{
	/** @var \SixtyEightPublishers\ArchitectureBundle\Domain\Command\CommandBusInterface @inject  */
	public CommandBusInterface $cb;

	protected function startup() : void
	{
		parent::startup();

		$id = '65dccfae-046b-4bfc-be75-2f15c7c9b709';

		$this->cb->dispatch(CreateUserCommand::create(
			Username::create('tg666'),
			Password::create('qqqqqqq'),
			EmailAddress::create('tomasglawaty@icloud.com'),
			Roles::empty()->withRole(Role::create('admin')),
			UserId::fromString($id)
		));
	}
}
