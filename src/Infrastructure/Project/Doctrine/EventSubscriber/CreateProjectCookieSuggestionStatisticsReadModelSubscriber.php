<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\EventSubscriber;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Tools\ToolEvents;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

final class CreateProjectCookieSuggestionStatisticsReadModelSubscriber implements EventSubscriber
{
	public function getSubscribedEvents(): array
	{
		return [
			ToolEvents::postGenerateSchema,
		];
	}

	/**
	 * @throws SchemaException
	 */
	public function postGenerateSchema(GenerateSchemaEventArgs $args): void
	{
		$schema = $args->getSchema();
		$table = $schema->createTable('project_cookie_suggestion_statistics');

		$table->addColumn('id', Types::BIGINT)
			->setNotnull(TRUE)
			->setAutoincrement(TRUE);

		$table->addColumn('project_id', Types::GUID)
			->setNotnull(TRUE);

		$table->addColumn('missing', Types::INTEGER)
			->setNotnull(TRUE);

		$table->addColumn('unassociated', Types::INTEGER)
			->setNotnull(TRUE);

		$table->addColumn('problematic', Types::INTEGER)
			->setNotnull(TRUE);

		$table->addColumn('unproblematic', Types::INTEGER)
			->setNotnull(TRUE);

		$table->addColumn('ignored', Types::INTEGER)
			->setNotnull(TRUE);

		$table->addColumn('total', Types::INTEGER)
			->setNotnull(TRUE);

		$table->addColumn('total_without_virtual', Types::INTEGER)
			->setNotnull(TRUE);

		$table->addColumn('latest_found_at', Types::DATETIME_IMMUTABLE)
			->setNotnull(FALSE);

		$table->setPrimaryKey(['id']);

		$table->addUniqueIndex(
			['project_id'],
			'uniq_pcss_project_id',
		);

		$table->addIndex(
			['project_id', 'latest_found_at'],
			'idx_pcss_project_id_latest_found_at',
		);

		$table->addForeignKeyConstraint(
			'project',
			['project_id'],
			['id'],
			[
				'onDelete' => 'CASCADE',
			]
		);
	}
}
