<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240126012912 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds table "user_external_auth" and column "global_settings"."azure_auth_settings".';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE user_external_auth (provider_code VARCHAR(100) NOT NULL, user_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, resource_owner_id VARCHAR(255) NOT NULL, token TEXT NOT NULL, refresh_token TEXT NOT NULL, PRIMARY KEY(provider_code, user_id))');
        $this->addSql('CREATE INDEX IDX_834F7D25A76ED395 ON user_external_auth (user_id)');
        $this->addSql('CREATE INDEX idx_user_ea_user_id_created_at ON user_external_auth (user_id, created_at)');
        $this->addSql('COMMENT ON COLUMN user_external_auth.provider_code IS \'(DC2Type:App\\Domain\\User\\ValueObject\\AuthProviderCode)\'');
        $this->addSql('COMMENT ON COLUMN user_external_auth.user_id IS \'(DC2Type:SixtyEightPublishers\\UserBundle\\Domain\\ValueObject\\UserId)\'');
        $this->addSql('COMMENT ON COLUMN user_external_auth.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_external_auth.resource_owner_id IS \'(DC2Type:App\\Domain\\User\\ValueObject\\AuthResourceOwnerId)\'');
        $this->addSql('COMMENT ON COLUMN user_external_auth.token IS \'(DC2Type:App\\Domain\\User\\ValueObject\\AuthToken)\'');
        $this->addSql('COMMENT ON COLUMN user_external_auth.refresh_token IS \'(DC2Type:App\\Domain\\User\\ValueObject\\AuthToken)\'');
        $this->addSql('ALTER TABLE user_external_auth ADD CONSTRAINT FK_834F7D25A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE global_settings ADD azure_auth_settings JSONB NOT NULL DEFAULT \'{}\'');
        $this->addSql('ALTER TABLE global_settings ALTER COLUMN azure_auth_settings DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN global_settings.azure_auth_settings IS \'(DC2Type:App\\Domain\\GlobalSettings\\ValueObject\\AzureAuthSettings)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_external_auth DROP CONSTRAINT FK_834F7D25A76ED395');
        $this->addSql('DROP TABLE user_external_auth');
        $this->addSql('ALTER TABLE global_settings DROP azure_auth_settings');
    }
}
