<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use Nette\DI\Container;
use Doctrine\Persistence\ObjectManager;
use Nettrine\Fixtures\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class GlobalSettingsFixture extends AbstractFixture implements ContainerAwareInterface
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

		$commandBus->dispatch(StoreGlobalSettingsCommand::create(['en', 'cs', 'de']));

		$manager->clear();
	}
}
