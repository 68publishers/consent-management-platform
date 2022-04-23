<?php

declare(strict_types=1);

namespace App\Bridge\Doctrine\EventSubscriber;

use Doctrine\ORM\Tools\ToolEvents;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

final class FixDefaultSchemaSubscriber implements EventSubscriber
{
	/**
	 * {@inheritDoc}
	 */
	public function getSubscribedEvents(): array
	{
		return [ToolEvents::postGenerateSchema];
	}

	/**
	 * @param \Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs $args
	 *
	 * @return void
	 * @throws \Doctrine\DBAL\Schema\SchemaException
	 */
	public function postGenerateSchema(GenerateSchemaEventArgs $args): void
	{
		$schemaManager = $args->getEntityManager()
			->getConnection()
			->getSchemaManager();

		if (!$schemaManager instanceof PostgreSQLSchemaManager) {
			return;
		}

		foreach ($schemaManager->getExistingSchemaSearchPaths() as $namespace) {
			if (!$args->getSchema()->hasNamespace($namespace)) {
				$args->getSchema()->createNamespace($namespace);
			}
		}
	}
}
