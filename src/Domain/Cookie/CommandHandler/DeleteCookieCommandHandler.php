<?php

declare(strict_types=1);

namespace App\Domain\Cookie\CommandHandler;

use App\Domain\Cookie\ValueObject\CookieId;
use App\Domain\Cookie\CookieRepositoryInterface;
use App\Domain\Cookie\Command\DeleteCookieCommand;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class DeleteCookieCommandHandler implements CommandHandlerInterface
{
	private CookieRepositoryInterface $cookieRepository;

	/**
	 * @param \App\Domain\Cookie\CookieRepositoryInterface $cookieRepository
	 */
	public function __construct(CookieRepositoryInterface $cookieRepository)
	{
		$this->cookieRepository = $cookieRepository;
	}

	/**
	 * @param \App\Domain\Cookie\Command\DeleteCookieCommand $command
	 *
	 * @return void
	 */
	public function __invoke(DeleteCookieCommand $command): void
	{
		$cookie = $this->cookieRepository->get(CookieId::fromString($command->cookieId()));

		$cookie->delete();

		$this->cookieRepository->save($cookie);
	}
}
