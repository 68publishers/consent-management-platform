<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20230629234324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "project"."domain" and fills domains for existing rows with codes.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project ADD domain VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->addSql('COMMENT ON COLUMN project.domain IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\Domain)\'');

        $existingProjectRows = $this->connection->createQueryBuilder()
            ->select('p.id, p.code')
            ->from('project', 'p')
            ->fetchAllAssociative();

        foreach ($existingProjectRows as $existingProjectRow) {
            $updateSql = $this->connection->createQueryBuilder()
                ->update('project')
                ->set('domain', ':domain')
                ->where('id = :id')
                ->getSQL();

            $this->addSql(
                $updateSql,
                [
                    'domain' => $existingProjectRow['code'],
                    'id' => $existingProjectRow['id'],
                ],
                [
                    'domain' => Types::STRING,
                    'id' => Types::GUID,
                ],
            );
        }

        $this->addSql('ALTER TABLE project ALTER COLUMN domain DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE project DROP domain');
    }
}
