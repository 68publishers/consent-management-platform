<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use Nette\DI\Container;
use Nette\Utils\Random;
use Doctrine\Persistence\ObjectManager;
use Nettrine\Fixtures\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use App\Domain\Consent\Command\StoreConsentCommand;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class DemoConsentFixture extends AbstractFixture implements ContainerAwareInterface, DependentFixtureInterface
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
		$commandArgs = [];
		$updateCommandArgs = [];

		for ($i = 0; $i < 30; $i++) {
			$identifier = Random::generate(15, '0-9a-zA-Z');
			$commandArgs[$identifier] = [
				DemoProjectFixture::$projectId->toString(),
				$identifier,
				NULL,
				[
					'functionality_storage' => TRUE,
					'ad_storage' => 0 === $i % 2,
					'analytics_storage' => 0 === $i % 2,
				],
				[
					'trackingId' => Random::generate(10, '0-9'),
				],
			];

			if (0 === $i % 2) {
				$update = $commandArgs[$identifier];
				$update[3] = [
					'functionality_storage' => TRUE,
					'ad_storage' => FALSE,
					'analytics_storage' => TRUE,
				];

				$updateCommandArgs[$identifier] = $update;
			}
		}

		foreach ($commandArgs as $args) {
			$commandBus->dispatch(StoreConsentCommand::create(...$args));
		}

		foreach ($updateCommandArgs as $args) {
			$commandBus->dispatch(StoreConsentCommand::create(...$args));
		}

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
