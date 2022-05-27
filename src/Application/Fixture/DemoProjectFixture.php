<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use Nette\DI\Container;
use Doctrine\Persistence\ObjectManager;
use App\Domain\Project\ValueObject\ProjectId;
use Nettrine\Fixtures\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use App\Domain\Project\Command\CreateProjectCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class DemoProjectFixture extends AbstractFixture implements ContainerAwareInterface
{
	public static ProjectId $projectId;

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
		self::$projectId = ProjectId::new();

		$commandBus->dispatch(CreateProjectCommand::create(
			'Demo',
			'demo',
			'The demo project.',
			'#DB2777',
			TRUE,
			['en', 'cs'],
			'en',
			self::$projectId->toString()
		));

		$manager->clear();
	}
}
