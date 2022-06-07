<?php

declare(strict_types=1);

namespace App\Application\Fixture;

use Nette\DI\Container;
use Doctrine\Persistence\ObjectManager;
use Nettrine\Fixtures\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use App\Domain\CookieProvider\ValueObject\ProviderType;
use App\Domain\CookieProvider\Command\CreateCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Bus\CommandBusInterface;

final class CookieProviderFixture extends AbstractFixture implements ContainerAwareInterface
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

		$commandBus->dispatch(CreateCookieProviderCommand::create(
			'acme',
			ProviderType::FIRST_PARTY,
			'Acme website',
			'https://www.acme.com',
			[
				'cs' => 'Naše vlastní cookies, které jsou nutné pro provoz našeho webu.',
				'en' => 'Our own cookies that are necessary for the operation of our website.',
			],
		));

		$commandBus->dispatch(CreateCookieProviderCommand::create(
			'facebook_login',
			ProviderType::THIRD_PARTY,
			'Facebook Login',
			'https://www.facebook.com/about/privacy/',
			[
				'cs' => 'Platforma pro přihlášení skrze Facebook.',
				'en' => 'Facebook login platform.',
			],
		));

		$commandBus->dispatch(CreateCookieProviderCommand::create(
			'google_ads',
			ProviderType::THIRD_PARTY,
			'Google Ads',
			'https://policies.google.com/privacy',
			[
				'cs' => 'Platforma pro reklamu, retargeting a měření konverzí.',
				'en' => 'The platform for advertising, retargeting, and conversion measurement.',
			],
		));

		$manager->clear();
	}
}
