<?php

declare(strict_types=1);

namespace App\Domain\Cookie\CommandHandler;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\CookieRepositoryInterface;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\Domain\Cookie\CheckCategoryExistsInterface;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class UpdateCookieCommandHandler implements CommandHandlerInterface
{
	private CookieRepositoryInterface $cookieRepository;

	private CheckCategoryExistsInterface $checkCategoryExists;

	/**
	 * @param \App\Domain\Cookie\CookieRepositoryInterface    $cookieRepository
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface $checkCategoryExists
	 */
	public function __construct(CookieRepositoryInterface $cookieRepository, CheckCategoryExistsInterface $checkCategoryExists)
	{
		$this->cookieRepository = $cookieRepository;
		$this->checkCategoryExists = $checkCategoryExists;
	}

	/**
	 * @param \App\Domain\Cookie\Command\UpdateCookieCommand $command
	 *
	 * @return void
	 */
	public function __invoke(UpdateCookieCommand $command): void
	{
		$cookie = $this->cookieRepository->get(CookieId::fromString($command->cookieId()));

		$cookie->update($command, $this->checkCategoryExists);

		$this->cookieRepository->save($cookie);
	}
}
