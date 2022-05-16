<?php

declare(strict_types=1);

namespace App\Infrastructure\Migration\Doctrine;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220513213856 extends AbstractMigration
{
	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return 'Initializes event stream tables for all existing aggregate types.';
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function up(Schema $schema): void
	{
		$this->addSql('CREATE TABLE user_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_user_event_stream_event_id ON user_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_user_event_stream_aggregate_id ON user_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_user_event_stream_created_at ON user_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_user_event_stream_event_id ON user_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN user_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN user_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN user_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE TABLE consent_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_consent_event_stream_event_id ON consent_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_consent_event_stream_aggregate_id ON consent_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_consent_event_stream_created_at ON consent_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_consent_event_stream_event_id ON consent_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN consent_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN consent_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN consent_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE TABLE consent_settings_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_consent_settings_event_stream_event_id ON consent_settings_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_consent_settings_event_stream_aggregate_id ON consent_settings_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_consent_settings_event_stream_created_at ON consent_settings_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_consent_settings_event_stream_event_id ON consent_settings_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN consent_settings_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN consent_settings_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN consent_settings_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE TABLE password_request_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_password_request_event_stream_event_id ON password_request_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_password_request_event_stream_aggregate_id ON password_request_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_password_request_event_stream_created_at ON password_request_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_password_request_event_stream_event_id ON password_request_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN password_request_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN password_request_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN password_request_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
		$this->addSql('CREATE TABLE project_event_stream (id BIGSERIAL NOT NULL, event_id UUID NOT NULL, aggregate_id UUID NOT NULL, event_name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, parameters JSON NOT NULL, metadata JSONB NOT NULL, PRIMARY KEY(id))');
		$this->addSql('CREATE INDEX idx_project_event_stream_event_id ON project_event_stream (event_id)');
		$this->addSql('CREATE INDEX idx_project_event_stream_aggregate_id ON project_event_stream (aggregate_id)');
		$this->addSql('CREATE INDEX idx_project_event_stream_created_at ON project_event_stream (created_at)');
		$this->addSql('CREATE UNIQUE INDEX uniq_project_event_stream_event_id ON project_event_stream (event_id)');
		$this->addSql('COMMENT ON COLUMN project_event_stream.event_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\EventId)\'');
		$this->addSql('COMMENT ON COLUMN project_event_stream.aggregate_id IS \'(DC2Type:SixtyEightPublishers\\ArchitectureBundle\\Domain\\ValueObject\\AggregateId)\'');
		$this->addSql('COMMENT ON COLUMN project_event_stream.created_at IS \'(DC2Type:datetime_immutable)\'');
	}

	/**
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 *
	 * @return void
	 */
	public function down(Schema $schema): void
	{
		$this->addSql('DROP TABLE user_event_stream');
		$this->addSql('DROP TABLE consent_event_stream');
		$this->addSql('DROP TABLE consent_settings_event_stream');
		$this->addSql('DROP TABLE password_request_event_stream');
		$this->addSql('DROP TABLE project_event_stream');
	}
}
