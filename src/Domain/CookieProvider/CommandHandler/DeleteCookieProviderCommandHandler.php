<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\CommandHandler;

use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\CookieProviderRepositoryInterface;
use App\Domain\CookieProvider\Command\DeleteCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class DeleteCookieProviderCommandHandler implements CommandHandlerInterface
{
	private CookieProviderRepositoryInterface $cookieProviderRepository;

	/**
	 * @param \App\Domain\CookieProvider\CookieProviderRepositoryInterface $cookieProviderRepository
	 */
	public function __construct(CookieProviderRepositoryInterface $cookieProviderRepository)
	{
		$this->cookieProviderRepository = $cookieProviderRepository;
	}

	/**
	 * @param \App\Domain\CookieProvider\Command\DeleteCookieProviderCommand $command
	 *
	 * @return void
	 */
	public function __invoke(DeleteCookieProviderCommand $command): void
	{
		$cookieProvider = $this->cookieProviderRepository->get(CookieProviderId::fromString($command->cookieProviderId()));

		$cookieProvider->delete();

		$this->cookieProviderRepository->save($cookieProvider);
	}
}
