<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220831025836 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Changes unique constraints for short identifiers in the table "consent_settings" and remaps the data.';
    }

    /**
     * @throws Exception
     */
    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE app_consent_settings_short_identifier');
        $this->addSql('DROP INDEX uniq_ca814c72cfed4fbf');
        $this->upShortIdentifiers();
        $this->addSql('CREATE UNIQUE INDEX uniq_consent_settings_project_id_short_identifier ON consent_settings (project_id, short_identifier)');
    }

    /**
     * @throws Exception
     */
    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE app_consent_settings_short_identifier INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('DROP INDEX uniq_consent_settings_project_id_short_identifier');
        $this->downShortIdentifiers();
        $this->addSql('CREATE UNIQUE INDEX uniq_ca814c72cfed4fbf ON consent_settings (short_identifier)');
    }

    /**
     * @throws Exception
     */
    private function upShortIdentifiers(): void
    {
        $consentSettings = $this->connection->createQueryBuilder()
            ->select('id, project_id')
            ->from('consent_settings', 'cs')
            ->orderBy('created_at', 'ASC')
            ->executeQuery();

        $identifiersByProjectId = [];

        while ($row = $consentSettings->fetchAssociative()) {
            if (!isset($identifiersByProjectId[$row['project_id']])) {
                $identifiersByProjectId[$row['project_id']] = 0;
            }

            $identifiersByProjectId[$row['project_id']]++;

            $updateQuery = $this->connection->createQueryBuilder()
                ->update('consent_settings')
                ->set('short_identifier', ':short_identifier')
                ->where('id = :id')
                ->getSQL();

            $this->addSql(
                $updateQuery,
                [
                    'id' => $row['id'],
                    'short_identifier' => $identifiersByProjectId[$row['project_id']],
                ],
                [
                    'guid',
                    'integer',
                ],
            );
        }
    }

    /**
     * @throws Exception
     */
    private function downShortIdentifiers(): void
    {
        $consentSettings = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('consent_settings')
            ->orderBy('created_at', 'ASC')
            ->executeQuery();

        while ($row = $consentSettings->fetchAssociative()) {
            $updateQuery = $this->connection->createQueryBuilder()
                ->update('consent_settings', 'cs')
                ->set('short_identifier', '(SELECT nextval(\'app_consent_settings_short_identifier\'))')
                ->where('id = :id')
                ->getSQL();

            $this->addSql($updateQuery, ['id' => $row['id']], ['guid']);
        }
    }
}
