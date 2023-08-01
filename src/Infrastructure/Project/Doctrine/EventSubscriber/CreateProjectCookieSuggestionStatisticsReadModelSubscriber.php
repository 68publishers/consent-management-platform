<?php

declare(strict_types=1);

namespace App\Infrastructure\Project\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

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
            ->setNotnull(true)
            ->setAutoincrement(true);

        $table->addColumn('project_id', Types::GUID)
            ->setNotnull(true);

        $table->addColumn('missing', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('unassociated', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('problematic', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('unproblematic', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('ignored', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('total', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('total_without_virtual', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('latest_found_at', Types::DATETIME_IMMUTABLE)
            ->setNotnull(false);

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
            ],
        );
    }
}
