<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use Nette\DI\Container;
use App\Domain\User\RolesEnum;
use Doctrine\Persistence\ObjectManager;
use Nettrine\Fixtures\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\CreateUserCommand;

final class AdminUserFixture extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
{
	private Container $container;

	/**
	 * @param \Nette\DI\Container $container
	 *
	 * @return void
	 */
	public function setContainer(Container $container): void
	{
		$this->container = $container;
	}

	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager): void
	{
		$commandBus = $this->container->getByType(CommandBusInterface::class);

		$commandBus->dispatch(
			CreateUserCommand::create(
				'admin@68publishers.io',
				'admin',
				'admin@68publishers.io',
				'Admin',
				'SixtyEightPublishers',
				[RolesEnum::ADMIN]
			)->withParam('project_ids', [DemoProjectFixture::$projectId->toString()])
				->withParam('profile', 'cs')
				->withParam('timezone', 'Europe/Prague')
		);

		$manager->clear();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies(): array
	{
		return [
			DemoProjectFixture::class,
		];
	}
}
