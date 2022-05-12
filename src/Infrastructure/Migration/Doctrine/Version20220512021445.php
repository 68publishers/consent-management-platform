<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220512021445 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Initializes table for consent settings.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE consent_settings (id UUID NOT NULL, project_id UUID NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, last_update_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, checksum VARCHAR(255) NOT NULL, settings JSON NOT NULL, version INT NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_consent_settings_project_id ON consent_settings (project_id)');
		$this->addSql('CREATE INDEX idx_consent_settings_created_at ON consent_settings (created_at)');
		$this->addSql('CREATE INDEX idx_consent_settings_last_update_at ON consent_settings (last_update_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_consent_settings_project_id_checksum ON consent_settings (project_id, checksum)');
		$this->addSql('COMMENT ON COLUMN consent_settings.id IS \'(DC2Type:App\\Domain\\ConsentSettings\\ValueObject\\ConsentSettingsId)\'');
		$this->addSql('COMMENT ON COLUMN consent_settings.project_id IS \'(DC2Type:App\\Domain\\Project\\ValueObject\\ProjectId)\'');
		$this->addSql('COMMENT ON COLUMN consent_settings.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN consent_settings.last_update_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('COMMENT ON COLUMN consent_settings.checksum IS \'(DC2Type:App\\Domain\\Shared\\ValueObject\\Checksum)\'');
		$this->addSql('COMMENT ON COLUMN consent_settings.settings IS \'(DC2Type:App\\Domain\\ConsentSettings\\ValueObject\\SettingsGroup)\'');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP TABLE consent_settings');
	}
}
