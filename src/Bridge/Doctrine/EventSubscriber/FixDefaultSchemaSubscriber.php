<?php

declare(strict_types=1);

namespace App\Bridge\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\PostgreSQLSchemaManager;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

final class FixDefaultSchemaSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [ToolEvents::postGenerateSchema];
    }

    /**
     * @throws SchemaException
     * @throws Exception
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
