<?php

declare(strict_types=1);

namespace App\Infrastructure\ConsentSettings\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception as DbalException;
use App\Domain\ConsentSettings\ValueObject\ShortIdentifier;
use App\Domain\ConsentSettings\ShortIdentifierGeneratorInterface;
use App\Domain\ConsentSettings\Exception\ShortIdentifierGeneratorException;

final class ShortIdentifierGenerator implements ShortIdentifierGeneratorInterface
{
	public const SEQUENCE_NAME = 'consent_settings_short_identifier';

	private EntityManagerInterface $em;

	/**
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 */
	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

	/**
	 * {@inheritDoc}
	 */
	public function generate(): ShortIdentifier
	{
		$connection = $this->em->getConnection();

		try {
			$serial = (int) $connection->fetchOne(
				$connection->getDatabasePlatform()->getSequenceNextValSQL(self::SEQUENCE_NAME)
			);

			return ShortIdentifier::fromValue($serial);
		} catch (DbalException $e) {
			throw ShortIdentifierGeneratorException::from($e);
		}
	}
}
