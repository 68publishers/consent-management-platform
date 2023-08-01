<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220512211810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initializes table for consents.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE consent (id UUID NOT NULL, project_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, user_identifier VARCHAR(255) NOT NULL, settings_checksum VARCHAR(255) NOT NULL, consents JSON NOT NULL, attributes JSON NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_consent_project_id ON consent (project_id)');
        $this->addSql('CREATE INDEX idx_consent_created_at ON consent (created_at)');
        $this->addSql('CREATE INDEX idx_consent_last_update_at ON consent (last_update_at)');
        $this->addSql('CREATE UNIQUE INDEX uniq_consent_project_id_user_identifier ON consent (project_id, user_identifier)');
        $this->addSql('COMMENT ON COLUMN consent.id IS \'(DC2Type:App\\Domain\\Consent\\ValueObject\\ConsentId)\'');
        $this->addSql('COMMENT ON COLUMN consent.project_id IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\ProjectId)\'');
        $this->addSql('COMMENT ON COLUMN consent.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN consent.last_update_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN consent.user_identifier IS \'(DC2Type:App\\Domain\\Consent\\ValueObject\\UserIdentifier)\'');
        $this->addSql('COMMENT ON COLUMN consent.settings_checksum IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Checksum)\'');
        $this->addSql('COMMENT ON COLUMN consent.consents IS \'(DC2Type:App\\Domain\\Consent\\ValueObject\\Consents)\'');
        $this->addSql('COMMENT ON COLUMN consent.attributes IS \'(DC2Type:App\\Domain\\Consent\\ValueObject\\Attributes)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE consent');
    }
}
