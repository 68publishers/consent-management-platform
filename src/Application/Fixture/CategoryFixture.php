<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use Nette\DI\Container;
use Doctrine\Persistence\ObjectManager;
use Nettrine\Fixtures\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use App\Domain\Category\Command\CreateCategoryCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class CategoryFixture extends AbstractFixture implements ContainerAwareInterface
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

		$commandBus->dispatch(CreateCategoryCommand::create(
			'functionality_storage',
			[
				'cs' => 'Nezbytně nutné soubory cookies',
				'en' => 'Functionality cookies',
			],
			TRUE
		));

		$commandBus->dispatch(CreateCategoryCommand::create(
			'personalization_storage',
			[
				'cs' => 'Personalizační cookies',
				'en' => 'Personalization cookies',
			],
			TRUE
		));

		$commandBus->dispatch(CreateCategoryCommand::create(
			'security_storage',
			[
				'cs' => 'Bezpečnostní cookies',
				'en' => 'Security cookies',
			],
			TRUE
		));

		$commandBus->dispatch(CreateCategoryCommand::create(
			'ad_storage',
			[
				'cs' => 'Reklamní cookies',
				'en' => 'Ad cookies',
			],
			TRUE
		));

		$commandBus->dispatch(CreateCategoryCommand::create(
			'analytics_storage',
			[
				'cs' => 'Analytické cookies',
				'en' => 'Analytics cookies',
			],
			TRUE
		));

		$manager->clear();
	}
}
