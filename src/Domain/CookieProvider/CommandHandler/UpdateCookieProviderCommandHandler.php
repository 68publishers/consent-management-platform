<?php

declare(strict_types=1);

namespace App\Domain\CookieProvider\CommandHandler;

use App\Domain\CookieProvider\CheckCodeUniquenessInterface;
use App\Domain\CookieProvider\ValueObject\CookieProviderId;
use App\Domain\CookieProvider\CookieProviderRepositoryInterface;
use App\Domain\CookieProvider\Command\UpdateCookieProviderCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class UpdateCookieProviderCommandHandler implements CommandHandlerInterface
{
	private CookieProviderRepositoryInterface $cookieProviderRepository;

	private CheckCodeUniquenessInterface $checkCodeUniqueness;

	/**
	 * @param \App\Domain\CookieProvider\CookieProviderRepositoryInterface $cookieProviderRepository
	 * @param \App\Domain\CookieProvider\CheckCodeUniquenessInterface      $checkCodeUniqueness
	 */
	public function __construct(CookieProviderRepositoryInterface $cookieProviderRepository, CheckCodeUniquenessInterface $checkCodeUniqueness)
	{
		$this->cookieProviderRepository = $cookieProviderRepository;
		$this->checkCodeUniqueness = $checkCodeUniqueness;
	}

	/**
	 * @param \App\Domain\CookieProvider\Command\UpdateCookieProviderCommand $command
	 *
	 * @return void
	 */
	public function __invoke(UpdateCookieProviderCommand $command): void
	{
		$category = $this->cookieProviderRepository->get(CookieProviderId::fromString($command->cookieProviderId()));

		$category->update($command, $this->checkCodeUniqueness);

		$this->cookieProviderRepository->save($category);
	}
}
