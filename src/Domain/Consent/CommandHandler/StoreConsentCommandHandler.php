<?php

declare(strict_types=1);

namespace App\Domain\Consent\CommandHandler;

use App\Domain\Consent\Consent;
use App\Domain\Consent\ConsentRepositoryInterface;
use App\Domain\Consent\Command\StoreConsentCommand;
use App\Domain\Consent\CheckUserIdentifierNotExistsInterface;
use App\Domain\Consent\Exception\UserIdentifierExistsException;
use SixtyEightPublishers\ArchitectureBundle\Command\CommandHandlerInterface;

final class StoreConsentCommandHandler implements CommandHandlerInterface
{
	private ConsentRepositoryInterface $consentRepository;

	private CheckUserIdentifierNotExistsInterface $checkUserIdentifierNotExists;

	/**
	 * @param \App\Domain\Consent\ConsentRepositoryInterface            $consentRepository
	 * @param \App\Domain\Consent\CheckUserIdentifierNotExistsInterface $checkUserIdentifierNotExists
	 */
	public function __construct(ConsentRepositoryInterface $consentRepository, CheckUserIdentifierNotExistsInterface $checkUserIdentifierNotExists)
	{
		$this->consentRepository = $consentRepository;
		$this->checkUserIdentifierNotExists = $checkUserIdentifierNotExists;
	}

	/**
	 * @param \App\Domain\Consent\Command\StoreConsentCommand $command
	 *
	 * @return void
	 */
	public function __invoke(StoreConsentCommand $command): void
	{
		try {
			$consent = Consent::create($command, $this->checkUserIdentifierNotExists);
		} catch (UserIdentifierExistsException $e) {
			$consent = $this->consentRepository->get($e->consentId());
			$consent->update($command);
		}

		$this->consentRepository->save($consent);
	}
}
