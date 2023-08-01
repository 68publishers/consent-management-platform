<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use App\Domain\Category\Command\CreateCategoryCommand;
use App\Domain\Consent\Command\StoreConsentCommand;
use App\Domain\ConsentSettings\Command\StoreConsentSettingsCommand;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use App\Domain\GlobalSettings\Command\PutLocalizationSettingsCommand;
use App\Domain\Project\Command\CreateProjectCommand;
use App\Domain\User\Command\AssignProjectsToUserCommand;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nette\DI\Container;
use Nettrine\Fixtures\ContainerAwareInterface;
use Psr\Log\LoggerInterface;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;
use SixtyEightPublishers\UserBundle\Domain\Command\CreateUserCommand;

abstract class AbstractFixture implements FixtureInterface, ContainerAwareInterface
{
    private CommandBusInterface $commandBus;

    private LoggerInterface $logger;

    public function setContainer(Container $container): void
    {
        $this->commandBus = $container->getByType(CommandBusInterface::class);
        $this->logger = $container->getByType(LoggerInterface::class);
    }

    public function load(ObjectManager $manager): void
    {
        $commandsByTypes = [
            'global_settings_localization' => PutLocalizationSettingsCommand::class,
            'category' => CreateCategoryCommand::class,
            'cookie_provider' => CreateCookieProviderCommand::class,
            'cookie' => CreateCookieCommand::class,
            'project' => CreateProjectCommand::class,
            'user' => CreateUserCommand::class,
            'consent' => StoreConsentCommand::class,
            'consent_settings' => StoreConsentSettingsCommand::class,
            'user_has_projects' => AssignProjectsToUserCommand::class,
        ];

        $fixtures = $this->loadFixtures($manager);
        $i = 0;

        foreach ($commandsByTypes as $type => $commandClassname) {
            if (!isset($fixtures[$type])) {
                continue;
            }

            $this->logger->info(sprintf(
                'Loading fixtures of type "%s".',
                $type,
            ));

            foreach ($fixtures[$type] as $fixture) {
                $this->commandBus->dispatch(([$commandClassname, 'fromParameters'])($fixture));
                $i++;

                if (50 === $i) {
                    $manager->clear();
                    $i = 0;
                }
            }

            $manager->clear();
        }
    }

    abstract protected function loadFixtures(ObjectManager $manager): array;
}
