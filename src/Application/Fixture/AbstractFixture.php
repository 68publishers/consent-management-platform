<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use Nette\DI\Container;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ObjectManager;
use Nettrine\Fixtures\ContainerAwareInterface;
use App\Domain\Cookie\Command\CreateCookieCommand;
use Doctrine\Common\DataFixtures\FixtureInterface;
use App\Domain\Consent\Command\StoreConsentCommand;
use App\Domain\Project\Command\CreateProjectCommand;
use App\Domain\Category\Command\CreateCategoryCommand;
use App\Domain\GlobalSettings\Command\StoreGlobalSettingsCommand;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\CreateUserCommand;

abstract class AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
	private CommandBusInterface $commandBus;

	private LoggerInterface $logger;

	/**
	 * @param \Nette\DI\Container $container
	 *
	 * @return void
	 */
	public function setContainer(Container $container): void
	{
		$this->commandBus = $container->getByType(CommandBusInterface::class);
		$this->logger = $container->getByType(LoggerInterface::class);
	}

	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager): void
	{
		$commandsByTypes = [
			'global_settings' => StoreGlobalSettingsCommand::class,
			'category' => CreateCategoryCommand::class,
			'cookie_provider' => CreateCookieProviderCommand::class,
			'cookie' => CreateCookieCommand::class,
			'project' => CreateProjectCommand::class,
			'user' => CreateUserCommand::class,
			'consent' => StoreConsentCommand::class,
		];

		$fixtures = $this->loadFixtures();

		foreach ($commandsByTypes as $type => $commandClassname) {
			if (!isset($fixtures[$type])) {
				continue;
			}

			$this->logger->info(sprintf(
				'Loading fixtures of type "%s".',
				$type
			));

			foreach ($fixtures[$type] as $fixture) {
				$this->commandBus->dispatch(([$commandClassname, 'fromParameters'])($fixture));
			}

			$manager->clear();
		}
	}

	/**
	 * @return array
	 */
	abstract protected function loadFixtures(): array;
}
