<?php

declare(strict_types=1);

namespace App\Domain\Cookie\CommandHandler;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\CookieRepositoryInterface;
use App\Domain\Cookie\Command\UpdateCookieCommand;
use App\Domain\Cookie\CheckCategoryExistsInterface;
use App\Domain\Cookie\CheckNameUniquenessInterface;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class UpdateCookieCommandHandler implements CommandHandlerInterface
{
	private CookieRepositoryInterface $cookieRepository;

	private CheckCategoryExistsInterface $checkCategoryExists;

	private CheckNameUniquenessInterface $checkNameUniqueness;

	/**
	 * @param \App\Domain\Cookie\CookieRepositoryInterface    $cookieRepository
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface $checkCategoryExists
	 * @param \App\Domain\Cookie\CheckNameUniquenessInterface $checkNameUniqueness
	 */
	public function __construct(CookieRepositoryInterface $cookieRepository, CheckCategoryExistsInterface $checkCategoryExists, CheckNameUniquenessInterface $checkNameUniqueness)
	{
		$this->cookieRepository = $cookieRepository;
		$this->checkCategoryExists = $checkCategoryExists;
		$this->checkNameUniqueness = $checkNameUniqueness;
	}

	/**
	 * @param \App\Domain\Cookie\Command\UpdateCookieCommand $command
	 *
	 * @return void
	 */
	public function __invoke(UpdateCookieCommand $command): void
	{
		$cookie = $this->cookieRepository->get(CookieId::fromString($command->cookieId()));

		$cookie->update($command, $this->checkCategoryExists, $this->checkNameUniqueness);

		$this->cookieRepository->save($cookie);
	}
}
