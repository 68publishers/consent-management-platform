<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231001234332 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds column "consent"."environment".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE consent ADD environment VARCHAR(255) NOT NULL DEFAULT \'default\'');
        $this->addSql('ALTER TABLE consent ALTER COLUMN environment DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN consent.environment IS \'(DC2Type:App\\Domain\\Consent\\ValueObject\\Environment)\'');
        $this->addSql('CREATE INDEX idx_consent_project_id_environment ON consent (project_id, environment)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_consent_project_id_environment');
        $this->addSql('ALTER TABLE consent DROP environment');
    }
}
