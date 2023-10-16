<?php

declare(strict_types=1);

namespace App\Infrastructure\Consent\Doctrine\ProjectionModel;

use App\Projection\Consent\ConsentStatisticsProjection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use SixtyEightPublishers\ProjectionBundle\Infrastructure\Doctrine\AbstractProjectionModel;

final class ConsentStatisticsProjectionModel extends AbstractProjectionModel
{
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct('consent_statistics_projection', $em);
    }

    public static function projectionClassname(): string
    {
        return ConsentStatisticsProjection::class;
    }

    public function createSchema(Schema $schema): void
    {
        $table = $schema->createTable($this->getTableName());

        $table->addColumn('id', Types::BIGINT)
            ->setNotnull(true)
            ->setAutoincrement(true);

        $table->addColumn('project_id', Types::GUID)
            ->setNotnull(true);

        $table->addColumn('consent_id', Types::GUID)
            ->setNotnull(true);

        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE)
            ->setNotnull(true);

        $table->addColumn('environment', Types::STRING)
            ->setNotnull(true);

        $table->addColumn('positive_count', Types::INTEGER)
            ->setNotnull(true);

        $table->addColumn('negative_count', Types::INTEGER)
            ->setNotnull(true);

        $table->setPrimaryKey(['id']);

        $table->addIndex(
            columnNames: ['consent_id'],
            indexName: 'idx_csp_consent_id',
        );

        $table->addIndex(
            columnNames: ['project_id', 'created_at'],
            indexName: 'idx_csp_project_id_created_at',
        );
        $table->addIndex(
            columnNames: ['project_id', 'environment', 'created_at'],
            indexName: 'idx_csp_project_id_environment_created_at',
        );

        $table->addIndex(
            columnNames: ['project_id', 'consent_id', 'created_at'],
            indexName: 'idx_csp_project_id_consent_id_created_at_include',
            options: [
                'include' => ['positive_count', 'negative_count'],
                'desc' => 'created_at',
            ],
        );
        $table->addIndex(
            columnNames: ['project_id', 'environment', 'consent_id', 'created_at'],
            indexName: 'idx_csp_project_id_environment_consent_id_created_at_include',
            options: [
                'include' => ['positive_count', 'negative_count'],
                'desc' => 'created_at',
            ],
        );
    }
}
