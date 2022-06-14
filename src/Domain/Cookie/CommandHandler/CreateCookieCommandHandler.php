<?php

declare(strict_types=1);

namespace App\Domain\Cookie\CommandHandler;

use App\Domain\Cookie\Cookie;
use App\Domain\Cookie\CookieRepositoryInterface;
use App\Domain\Cookie\Command\CreateCookieCommand;
use App\Domain\Cookie\CheckCategoryExistsInterface;
use App\Domain\Cookie\CheckCookieProviderExistsInterface;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class CreateCookieCommandHandler implements CommandHandlerInterface
{
	private CookieRepositoryInterface $cookieRepository;

	private CheckCategoryExistsInterface $checkCategoryExists;

	private CheckCookieProviderExistsInterface $checkCookieProviderExists;

	/**
	 * @param \App\Domain\Cookie\CookieRepositoryInterface          $cookieRepository
	 * @param \App\Domain\Cookie\CheckCategoryExistsInterface       $checkCategoryExists
	 * @param \App\Domain\Cookie\CheckCookieProviderExistsInterface $checkCookieProviderExists
	 */
	public function __construct(CookieRepositoryInterface $cookieRepository, CheckCategoryExistsInterface $checkCategoryExists, CheckCookieProviderExistsInterface $checkCookieProviderExists)
	{
		$this->cookieRepository = $cookieRepository;
		$this->checkCategoryExists = $checkCategoryExists;
		$this->checkCookieProviderExists = $checkCookieProviderExists;
	}

	/**
	 * @param \App\Domain\Cookie\Command\CreateCookieCommand $command
	 *
	 * @return void
	 */
	public function __invoke(CreateCookieCommand $command): void
	{
		$cookie = Cookie::create($command, $this->checkCategoryExists, $this->checkCookieProviderExists);

		$this->cookieRepository->save($cookie);
	}
}
